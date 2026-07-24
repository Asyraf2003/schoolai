  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    .public-hero__actions .btn--ppdb-register{background:#137a4c;color:#fff;border:2px solid rgba(255,255,255,.72);box-shadow:0 16px 34px rgba(19,122,76,.34);font-weight:900;letter-spacing:.01em;text-shadow:0 1px 1px rgba(0,0,0,.26)}
    .public-hero__actions .btn--ppdb-register:hover{background:#0f6a41;box-shadow:0 20px 42px rgba(19,122,76,.42)}
    .public-hero__actions .btn--ppdb-guide{background:#fff;color:#20223f;border:2px solid rgba(19,122,76,.34);box-shadow:0 12px 26px rgba(32,34,63,.12);font-weight:900}
    .public-hero__actions .btn--ppdb-guide:hover{border-color:#137a4c;box-shadow:0 16px 34px rgba(32,34,63,.16)}
    .public-hero__actions .btn:focus-visible{outline:4px solid rgba(255,201,60,.75);outline-offset:4px}

    .ppdb-closed-modal{position:fixed;inset:0;z-index:1200;display:grid;place-items:center;padding:24px;opacity:0;visibility:hidden;pointer-events:none;transition:opacity .18s ease,visibility .18s ease}
    .ppdb-closed-modal:target{opacity:1;visibility:visible;pointer-events:auto}
    .ppdb-closed-modal__backdrop{position:absolute;inset:0;background:rgba(16,24,40,.52);backdrop-filter:blur(7px)}
    .ppdb-closed-modal__panel{position:relative;z-index:1;width:min(440px,100%);padding:28px;border-radius:28px;background:#fff;color:#20223f;box-shadow:0 26px 70px rgba(16,24,40,.24);text-align:center}
    .ppdb-closed-modal__icon{width:58px;height:58px;display:grid;place-items:center;margin:0 auto 14px;border-radius:22px;background:#fff2c6;font-size:1.9rem}
    .ppdb-closed-modal__panel h2{margin:0;font-size:clamp(1.45rem,4vw,1.9rem);line-height:1.12;letter-spacing:-.04em}
    .ppdb-closed-modal__panel p{margin:12px 0 0;color:#6b7280;line-height:1.7}
    .ppdb-closed-modal__close{display:inline-flex;align-items:center;justify-content:center;min-height:42px;margin-top:20px;padding:10px 18px;border-radius:999px;background:#20223f;color:#fff;font-weight:900;text-decoration:none}

    .ppdb-liftoff{position:relative;overflow:hidden;padding:clamp(76px,9vw,126px) 0;background:radial-gradient(circle at 11% 18%,rgba(255,159,90,.28),transparent 28%),radial-gradient(circle at 90% 9%,rgba(127,199,224,.36),transparent 30%),linear-gradient(180deg,#fffbf4 0%,#fff8ec 100%);color:#181229;transition:background .28s ease,color .28s ease}
    .ppdb-liftoff[data-active-audience=school]{background:radial-gradient(circle at 12% 14%,rgba(163,160,255,.22),transparent 28%),radial-gradient(circle at 86% 8%,rgba(255,159,90,.16),transparent 28%),linear-gradient(180deg,#181229 0%,#201b35 100%);color:#fff}
    .ppdb-liftoff::before{content:"";position:absolute;inset:32px 10px;border:2px dashed rgba(24,18,41,.13);border-radius:38px;pointer-events:none}
    .ppdb-liftoff[data-active-audience=school]::before{border-color:rgba(255,255,255,.22)}
    .ppdb-liftoff__top{position:relative;z-index:3;display:grid;justify-items:center;gap:22px;margin-bottom:clamp(70px,8vw,112px);text-align:center}
    .ppdb-liftoff__switch{position:relative;display:inline-grid;grid-template-columns:1fr 1fr;gap:4px;padding:7px;border-radius:999px;background:linear-gradient(#fffbf4,#fffbf4) padding-box,linear-gradient(90deg,#cfe6ff,#aaaafa 45%,#fa946c) border-box;border:3px solid transparent;box-shadow:0 16px 34px rgba(24,18,41,.09);isolation:isolate;transition:background .24s ease,box-shadow .24s ease}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__switch{background:linear-gradient(#181229,#181229) padding-box,linear-gradient(90deg,#a3a0ff,#fff 48%,#fa946c) border-box;box-shadow:0 18px 40px rgba(0,0,0,.24)}
    .ppdb-liftoff__switch::before{content:"";position:absolute;top:7px;left:7px;width:calc(50% - 9px);height:calc(100% - 14px);border-radius:999px;background:#181229;box-shadow:0 10px 22px rgba(24,18,41,.18);transform:translateX(0);transition:transform .24s ease,background .24s ease;z-index:-1}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__switch::before{transform:translateX(calc(100% + 4px));background:#fff;box-shadow:0 10px 22px rgba(255,255,255,.16)}
    .ppdb-liftoff__tab{min-width:min(40vw,178px);padding:15px 20px;border:0;background:transparent;border-radius:999px;color:#181229;font:inherit;font-weight:900;line-height:1;white-space:nowrap;position:relative;z-index:1;transition:color .2s ease,opacity .2s ease;cursor:pointer}
    .ppdb-liftoff__tab.is-active{color:#fff}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__tab{color:rgba(255,255,255,.86)}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__tab.is-active{color:#181229}
    .ppdb-liftoff__tab:disabled{opacity:.42;cursor:not-allowed}
    .ppdb-liftoff__top h2{max-width:780px;font-size:clamp(2rem,4.4vw,4.6rem);line-height:.98;letter-spacing:-.07em;font-weight:950}
    .ppdb-liftoff__top p{max-width:640px;color:rgba(24,18,41,.68);font-size:clamp(1rem,1.5vw,1.2rem);line-height:1.65}
    .ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__top p,.ppdb-liftoff[data-active-audience=school] .ppdb-liftoff__cta p{color:rgba(255,255,255,.72)}

    .ppdb-liftoff-panel{position:relative;min-height:1240px}
    .ppdb-liftoff-panel[hidden]{display:none}
    .ppdb-liftoff .reveal{transform:translateY(16px);transition:opacity .45s ease,transform .45s ease}
    .ppdb-liftoff__stack{position:relative;z-index:2;display:grid;gap:clamp(150px,18vw,280px)}
    .ppdb-liftoff__storyline{position:absolute;inset:0;z-index:1;pointer-events:none;overflow:visible}
    .ppdb-liftoff__storyline svg{display:block;width:100%;height:100%;overflow:visible}
    .ppdb-liftoff-story-path{fill:none;stroke-linecap:round;stroke-linejoin:round;vector-effect:non-scaling-stroke;opacity:0;will-change:stroke-dashoffset,opacity,filter}
    .ppdb-liftoff-story-frame{stroke-width:4.5;filter:drop-shadow(0 0 8px rgba(170,171,250,.28));transition:filter .28s ease}
    .ppdb-liftoff-story-frame.is-story-lit{filter:drop-shadow(0 0 12px rgba(255,77,141,.32)) drop-shadow(0 0 18px rgba(76,201,240,.28))}
    .ppdb-liftoff-story-frame.is-story-holding{filter:drop-shadow(0 0 18px rgba(255,77,141,.52)) drop-shadow(0 0 28px rgba(76,201,240,.44)) drop-shadow(0 0 36px rgba(199,125,255,.32))}
    .ppdb-liftoff-story-connector{stroke-width:5.5;filter:drop-shadow(0 0 9px rgba(76,201,240,.3))}
    .ppdb-liftoff-card{position:relative;z-index:2;display:grid;grid-template-columns:minmax(300px,.95fr) minmax(260px,.75fr);align-items:center;gap:clamp(54px,8vw,122px);min-height:640px}
    .ppdb-liftoff-card:nth-child(even){grid-template-columns:minmax(260px,.75fr) minmax(300px,.95fr)}
    .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__visual{order:2}
    .ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__text{order:1}
    .ppdb-liftoff-card__visual{position:relative;min-height:clamp(350px,40vw,500px);aspect-ratio:16/11;border-radius:28px;overflow:hidden;background:linear-gradient(135deg,rgba(255,46,125,.92),rgba(255,102,33,.84) 36%,rgba(210,231,255,.8) 100%),#f7f1f0;box-shadow:0 28px 72px rgba(24,18,41,.14);isolation:isolate}
    .ppdb-liftoff-card:nth-child(3n + 2) .ppdb-liftoff-card__visual{background:linear-gradient(135deg,rgba(210,231,255,.92),rgba(170,171,250,.9) 46%,rgba(250,148,108,.76) 100%),#f7f1f0}
    .ppdb-liftoff-card:nth-child(3n) .ppdb-liftoff-card__visual{background:linear-gradient(135deg,rgba(255,201,60,.78),rgba(126,217,180,.74) 42%,rgba(183,163,224,.82) 100%),#f7f1f0}
    .ppdb-liftoff-panel--school .ppdb-liftoff-card__visual{background:linear-gradient(135deg,rgba(163,160,255,.88),rgba(75,68,140,.9) 48%,rgba(249,115,22,.44) 100%),#221d3d;box-shadow:0 28px 72px rgba(0,0,0,.24)}
    .ppdb-liftoff-media{position:absolute;inset:0;z-index:2;display:block;background:#111827;pointer-events:auto}
    .ppdb-liftoff-media img,.ppdb-liftoff-media iframe{position:absolute;inset:0;width:100%;height:100%;border:0;display:block;background:#111827}
    .ppdb-liftoff-media img{object-fit:cover}
    .ppdb-liftoff-media iframe{touch-action:manipulation;pointer-events:auto}
    .ppdb-liftoff-media--image::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(24,18,41,.02),rgba(24,18,41,.18));pointer-events:none}
    .ppdb-liftoff-ui{position:absolute;inset:10% 9% auto;display:grid;gap:22px}
    .ppdb-liftoff-ui__panel{padding:clamp(20px,3vw,30px);border-radius:24px;background:#fff;box-shadow:0 18px 46px rgba(24,18,41,.16)}
    .ppdb-liftoff-ui__panel h3{margin:0 0 14px;font-size:clamp(1rem,1.4vw,1.2rem);letter-spacing:-.03em}
    .ppdb-liftoff-ui__line{height:12px;border-radius:999px;background:rgba(170,171,250,.28)}
    .ppdb-liftoff-ui__line+.ppdb-liftoff-ui__line{width:72%;margin-top:10px}
    .ppdb-liftoff-list{display:grid;gap:12px}
    .ppdb-liftoff-list__item{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px;border-radius:16px;background:#fff;border:1px solid rgba(24,18,41,.06);box-shadow:0 10px 26px rgba(24,18,41,.07);color:rgba(24,18,41,.72);font-weight:800}
    .ppdb-liftoff-list__item span{width:36px;height:36px;display:grid;place-items:center;border-radius:12px;background:#181229;color:#fff;flex:0 0 auto}
    .ppdb-liftoff-card__text{position:relative;justify-self:center;width:100%;max-width:520px;padding:clamp(24px,3vw,38px);box-sizing:border-box;border-radius:clamp(24px,3vw,34px);transition:box-shadow .3s ease,filter .3s ease}
    .ppdb-liftoff-card__text.is-story-lit{box-shadow:0 0 22px rgba(255,77,141,.12),0 0 36px rgba(76,201,240,.14),0 0 52px rgba(199,125,255,.11);filter:drop-shadow(0 14px 22px rgba(108,99,255,.08))}
    .ppdb-liftoff-card__text.is-story-holding{box-shadow:0 0 28px rgba(255,77,141,.24),0 0 48px rgba(76,201,240,.25),0 0 70px rgba(199,125,255,.2);filter:drop-shadow(0 18px 28px rgba(108,99,255,.14))}
    .ppdb-liftoff-step{width:42px;height:42px;display:grid;place-items:center;margin-bottom:22px;border-radius:999px;background:linear-gradient(#fffbf4,#fffbf4) padding-box,linear-gradient(135deg,#cfe6ff,#aaaafa 55%,#fa946c) border-box;border:3px solid transparent;color:#181229;font-weight:950;transition:box-shadow .3s ease}
    .ppdb-liftoff-card__text.is-story-holding .ppdb-liftoff-step{box-shadow:0 0 18px rgba(255,77,141,.3),0 0 30px rgba(76,201,240,.26)}
    .ppdb-liftoff-panel--school .ppdb-liftoff-step{background:linear-gradient(#181229,#181229) padding-box,linear-gradient(135deg,#a3a0ff,#fff 55%,#fa946c) border-box;color:#fff}
    .ppdb-liftoff-card__text h3{margin:0;font-size:clamp(1.8rem,3.2vw,3.2rem);line-height:1.02;letter-spacing:-.065em;font-weight:950}
    .ppdb-liftoff-card__text p{margin-top:20px;color:rgba(24,18,41,.68);font-size:clamp(1rem,1.35vw,1.16rem);line-height:1.65}
    .ppdb-liftoff-panel--school .ppdb-liftoff-card__text p{color:rgba(255,255,255,.72)}
    .ppdb-liftoff__cta{position:relative;z-index:2;display:grid;justify-items:center;gap:18px;max-width:720px;margin:clamp(70px,8vw,110px) auto 0;text-align:center}
    .ppdb-liftoff__cta p{color:rgba(24,18,41,.68);font-size:1.04rem;line-height:1.7}
    @media (max-width:900px){.ppdb-liftoff::before{inset:16px;border-radius:28px}.ppdb-liftoff-panel{min-height:0}.ppdb-liftoff__stack{gap:72px}.ppdb-liftoff-card,.ppdb-liftoff-card:nth-child(even){grid-template-columns:1fr;min-height:0}.ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__visual,.ppdb-liftoff-card:nth-child(even) .ppdb-liftoff-card__text{order:initial}.ppdb-liftoff-card__text{max-width:none;justify-self:start}}
    @media (max-width:900px){.ppdb-liftoff-card__visual{aspect-ratio:16/10}.ppdb-liftoff-media iframe{min-height:100%}}
    @media (max-width:620px){.ppdb-liftoff{padding-block:64px}.ppdb-liftoff__switch{width:100%}.ppdb-liftoff__tab{min-width:0;padding-inline:12px;font-size:.9rem}.ppdb-liftoff-card__visual{min-height:320px;aspect-ratio:4/3;border-radius:24px}.ppdb-liftoff-ui{inset:9% 7% auto}.ppdb-liftoff-card__text{padding:24px 22px;border-radius:24px}}
  </style>
