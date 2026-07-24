        const root = document.querySelector('[data-ppdb-liftoff]');
        if (!root) return;

        const tabs = Array.from(root.querySelectorAll('[data-ppdb-liftoff-tab]'));
        const panels = Array.from(root.querySelectorAll('[data-ppdb-liftoff-panel]'));
        const liftoffReveals = Array.from(root.querySelectorAll('.reveal'));
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const svgNamespace = 'http://www.w3.org/2000/svg';
        const storylineState = new WeakMap();
        let storylineSequence = 0;
        let rebuildFrame = 0;

        const clamp = (value, min = 0, max = 1) => Math.min(Math.max(value, min), max);
        const smoothstep = (value) => value * value * (3 - (2 * value));
        const createSvgElement = (name) => document.createElementNS(svgNamespace, name);

        const revealVisibleItems = (scope = root) => {
          Array.from(scope.querySelectorAll('.reveal')).forEach((element) => {
            const rect = element.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            if (rect.top < viewportHeight * 1.08 && rect.bottom > -viewportHeight * 0.18) {
              element.classList.add('is-visible');
            }
          });
        };

        if ('IntersectionObserver' in window && liftoffReveals.length) {
          const earlyRevealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
              if (!entry.isIntersecting) return;

              entry.target.classList.add('is-visible');
              earlyRevealObserver.unobserve(entry.target);
            });
          }, {
            root: null,
            rootMargin: '44% 0px 10% 0px',
            threshold: 0.01,
          });

          liftoffReveals.forEach((element) => earlyRevealObserver.observe(element));
        }

        const roundedFramePath = (x, y, width, height, radius) => {
          const right = x + width;
          const bottom = y + height;
          const centerX = x + (width / 2);

          return [
            `M ${centerX} ${y}`,
            `H ${right - radius}`,
            `Q ${right} ${y} ${right} ${y + radius}`,
            `V ${bottom - radius}`,
            `Q ${right} ${bottom} ${right - radius} ${bottom}`,
            `H ${x + radius}`,
            `Q ${x} ${bottom} ${x} ${bottom - radius}`,
            `V ${y + radius}`,
            `Q ${x} ${y} ${x + radius} ${y}`,
            `H ${centerX}`,
          ].join(' ');
        };

        const connectorPath = ({ startX, startY, endX, endY, viaX, viaY, visualHeight }) => {
          const firstDrop = clamp((viaY - startY) * 0.58, 150, 330);
          const hangingDepth = clamp(visualHeight * 0.55, 180, 340);
          const approachDirection = viaX >= endX ? 1 : -1;
          const approachReach = clamp(Math.abs(viaX - endX) * 0.48, 100, 240);
          const controlEndX = endX + (approachDirection * approachReach);
          const controlEndY = endY + clamp(hangingDepth * 0.42, 80, 150);

          return [
            `M ${startX} ${startY}`,
            `C ${startX} ${startY + firstDrop}, ${viaX} ${viaY - (firstDrop * 0.24)}, ${viaX} ${viaY}`,
            `C ${viaX} ${viaY + hangingDepth}, ${controlEndX} ${controlEndY}, ${endX} ${endY}`,
          ].join(' ');
        };

        const preparePath = (path, start, end) => {
          const length = path.getTotalLength();
          path.style.strokeDasharray = String(length);
          path.style.strokeDashoffset = String(length);
          path.style.opacity = prefersReducedMotion ? '1' : '0';

          return {
            path,
            length,
            start,
            end: Math.max(end, start + 1),
          };
        };
