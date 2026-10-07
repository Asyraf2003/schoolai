// Kept as adapter metadata so the production minifier retains attribution.
export const programTypeLicense = `Adapted from Codrops KineticTypePageTransition (MIT).
Copyright (c) 2009 - 2021 Codrops, https://tympanus.net/codrops

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.`;
// Motion data follows the accepted reference.
// Only detail transitions use this adapter; final card geometry belongs to CSS.
export function programTypeIn(gsap, element, lines, rtl) {
    return gsap.timeline({ paused: true })
        .to(element, { duration: 1.4, ease: 'power2.inOut', scale: 2.7, rotate: rtl ? 90 : -90 })
        .to(lines, {
            keyframes: [
                { x: rtl ? '-20%' : '20%', duration: 1, ease: 'power1.inOut' },
                { x: rtl ? '200%' : '-200%', duration: 1.5, ease: 'power1.in' },
            ], stagger: .04,
        }, 0)
        .to(lines, { keyframes: [
            { opacity: 1, duration: 1, ease: 'power1.in' },
            { opacity: 0, duration: 1.5, ease: 'power1.in' },
        ] }, 0);
}

export function programTypeOut(gsap, element, lines) {
    return gsap.timeline({ paused: true })
        .to(element, { duration: 1.4, ease: 'power2.inOut', scale: 1, rotate: 0 }, 1.2)
        .to(lines, { duration: 2.3, ease: 'back', x: '0%', stagger: -.04 }, 0)
        .to(lines, { keyframes: [
            { opacity: 1, duration: 1, ease: 'power1.in' },
            { opacity: .16, duration: 1.5, ease: 'power1.in' },
        ] }, 0);
}
