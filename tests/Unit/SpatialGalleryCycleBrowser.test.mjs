import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
const base=process.env.SCHOOLAI_BROWSER_URL, modulePath=process.env.PROGRAM_PLAYWRIGHT_MODULE;
const out=process.env.PROGRAM_PROOF_DIRECTORY??'/tmp/shared-proof';
test('shared track remains continuous through forward/reverse seams and small datasets', {skip:!base||!modulePath}, async()=>{
    const {chromium}=await import(modulePath);
    const browser=await chromium.launch({executablePath:'/usr/bin/chromium',args:['--enable-unsafe-swiftshader']});
    await fs.mkdir(out,{recursive:true});
    const context=await browser.newContext({viewport:{width:1440,height:900},recordVideo:{dir:out,size:{width:1440,height:900}}});
    const page=await context.newPage();const errors=[],proof=[];page.on('pageerror',e=>errors.push(e.message));
    const pos=()=>page.locator('[data-spatial-gallery]').getAttribute('data-spatial-position').then(Number);
    const ready=async()=>{await page.waitForFunction(()=>+document.querySelector('[data-spatial-gallery]')?.dataset.spatialTextures>=1);await page.waitForTimeout(700);};
    const travel=async(slots)=>{
        const scale=await page.locator('[data-spatial-gallery]').evaluate(e=>+e.dataset.spatialPitch/(16*Math.tan(42*Math.PI/360)/e.clientHeight));
        await page.mouse.wheel(slots*scale,0);await page.waitForTimeout(1050);
    };
    const openPanel=async p=>{
        const point=await p.locator('[data-spatial-instance]').evaluateAll(links=>links.map(link=>{
            const values=link.style.clipPath.match(/-?[\d.]+px/g)?.map(parseFloat)??[];
            const xs=values.filter((_,i)=>i%2===0),ys=values.filter((_,i)=>i%2===1);
            return {x:xs.reduce((a,b)=>a+b,0)/xs.length,y:ys.reduce((a,b)=>a+b,0)/ys.length};
        }).filter(p=>p.x>20&&p.x<innerWidth-20).sort((a,b)=>Math.abs(a.x-innerWidth/2)-Math.abs(b.x-innerWidth/2))[0]);
        await p.mouse.click(point.x,point.y);
    };
    const monitor=()=>{
        window.trackFrames=[];window.trackRecording=true;
        const record=time=>{
            if(!window.trackRecording)return;
            const root=document.querySelector('[data-spatial-gallery]');
            window.trackFrames.push({time,progress:+root.dataset.spatialPosition,slots:[...root.querySelectorAll('[data-spatial-instance]')].map(e=>({slot:+e.dataset.spatialInstance,media:+e.dataset.spatialMedia,clip:e.style.clipPath,loaded:e.style.visibility!=='hidden'}))});
            requestAnimationFrame(record);
        };requestAnimationFrame(record);
    };
    try {
        await page.goto(base+'/galeri');await ready();await page.mouse.move(720,430);
        await page.screenshot({path:`${out}/center-1440x900.png`});
        await travel(.65);await page.screenshot({path:`${out}/side-to-center.png`});
        const count=await page.locator('[data-spatial-panel]').count();
        // Reach the last media through native wheel input, never a test-only state setter.
        while(await pos()<count-1.4) await travel(Math.min(1,count-1.4-await pos()));
        await page.evaluate(monitor);
        for(let i=0;i<16;i++){
            const scale=await page.locator('[data-spatial-gallery]').evaluate(e=>+e.dataset.spatialPitch/(16*Math.tan(42*Math.PI/360)/e.clientHeight));
            await page.mouse.wheel(scale*.12,0);await page.waitForTimeout(80);
            if([0,4,8,12,15].includes(i))await page.screenshot({path:`${out}/seam-${String(i).padStart(2,'0')}.png`});
        }
        await page.waitForTimeout(1000);
        const frames=await page.evaluate(()=>{window.trackRecording=false;return window.trackFrames;});
        assert.ok(frames.some(f=>f.progress<count));assert.ok(frames.some(f=>f.progress>count));
        for(let i=1;i<frames.length;i++){
            assert.ok(frames[i].progress>=frames[i-1].progress-.00001,'progress cannot reset at seam');
            assert.ok(frames[i].progress-frames[i-1].progress<.2,'no progress discontinuity');
            for(const old of frames[i-1].slots.filter(old=>!frames[i].slots.some(s=>s.slot===old.slot))){
                const xs=old.clip.match(/-?[\d.]+px/g)?.map(parseFloat).filter((_,j)=>j%2===0)??[];
                assert.ok(!xs.length||Math.max(...xs)<0||Math.min(...xs)>1440,'recycle only after every vertex leaves viewport');
            }
            for(const item of frames[i].slots){
                assert.equal(item.media,((item.slot%count)+count)%count);
                const previous=frames[i-1].slots.find(p=>p.slot===item.slot);
                if(!previous)continue;
                const a=previous.clip.match(/-?[\d.]+px/g)?.map(parseFloat),b=item.clip.match(/-?[\d.]+px/g)?.map(parseFloat);
                if(a?.length!==b?.length||!a||!b)continue;
                const max=Math.max(...a.map((v,j)=>Math.abs(v-b[j])));
                assert.ok(max<90,`mesh/hit outline discontinuity ${max}px at ${item.slot}`);
            }
        }
        proof.push({name:'continuous last-to-first native wheel sequence',frames:frames.length,from:frames[0].progress,to:frames.at(-1).progress});
        await fs.writeFile(`${out}/seam-frames.json`,JSON.stringify(frames));
        await travel(-count-2);assert.ok(await pos()<0);
        await travel(-2);assert.ok(await pos()<-2);proof.push({name:'negative progress without start boundary',progress:await pos()});
        await openPanel(page);assert.equal(await page.locator('dialog').evaluate(d=>d.open),true);
        await page.keyboard.press('Escape');
        for(const amount of [1,2,40]){
            const small=await browser.newContext({viewport:{width:1440,height:900}});
            // A DOM fixture built only from real school media, without database changes.
            await small.addInitScript(amount=>document.addEventListener('DOMContentLoaded',()=>{
                const links=[...document.querySelectorAll('[data-spatial-panel]')],track=document.querySelector('[data-spatial-track]');
                if(!track)return;
                if(amount<links.length)links.slice(amount).forEach(e=>e.remove());
                else for(let i=links.length;i<amount;i++)track.append(links[i%links.length].cloneNode(true));
            },{once:true}),amount);
            const p=await small.newPage();await p.goto(base+'/galeri');
            await p.waitForFunction(()=>+document.querySelector('[data-spatial-gallery]')?.dataset.spatialTextures>=1);
            await p.waitForTimeout(900);
            assert.equal(await p.locator('[data-spatial-panel]').count(),amount);
            assert.ok(await p.locator('[data-spatial-instance]').count()>=3);
            const before=await p.locator('[data-spatial-gallery]').getAttribute('data-spatial-position').then(Number);
            await p.mouse.move(720,430);await p.mouse.wheel(1800,0);await p.waitForTimeout(1500);
            const after=await p.locator('[data-spatial-gallery]').getAttribute('data-spatial-position').then(Number);
            assert.ok(after>before+1);
            assert.ok(+(await p.locator('[data-spatial-gallery]').getAttribute('data-spatial-textures'))<=5);
            await openPanel(p);assert.equal(await p.locator('dialog').evaluate(d=>d.open),true);await p.keyboard.press('Escape');
            await p.screenshot({path:`${out}/dataset-${amount}.png`});
            proof.push({name:'virtual instances and shared textures',media:amount,before,after});await small.close();
        }
        assert.deepEqual(errors,[]);
        await fs.writeFile(`${out}/cycles.json`,JSON.stringify({engine:browser.version(),proof,errors},null,2));
    }finally{const video=page.video();await context.close();await video.saveAs(`${out}/cycles.webm`);await browser.close();}
});
