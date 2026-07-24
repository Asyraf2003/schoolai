        const updateStoryline = (panel) => {
          const state = storylineState.get(panel);
          if (!state) return;

          if (prefersReducedMotion) {
            state.stages.forEach(({ path }) => {
              path.style.strokeDashoffset = '0';
              path.style.opacity = '1';
            });
            state.glowStages.forEach(({ node, frame }) => {
              node.classList.add('is-story-lit');
              node.classList.remove('is-story-holding');
              frame.classList.add('is-story-lit');
              frame.classList.remove('is-story-holding');
            });
            return;
          }

          const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
          const panelRect = panel.getBoundingClientRect();
          const traveled = (viewportHeight * 0.78) - panelRect.top;

          state.stages.forEach(({ path, length, start, end }) => {
            const rawProgress = clamp((traveled - start) / (end - start));
            const progress = smoothstep(rawProgress);
            path.style.strokeDashoffset = String(length * (1 - progress));
            path.style.opacity = rawProgress > 0 ? '1' : '0';
          });

          state.glowStages.forEach(({ node, frame, start, holdEnd }) => {
            const isLit = traveled >= start;
            const isHolding = isLit && traveled < holdEnd;
            node.classList.toggle('is-story-lit', isLit);
            node.classList.toggle('is-story-holding', isHolding);
            frame.classList.toggle('is-story-lit', isLit);
            frame.classList.toggle('is-story-holding', isHolding);
          });
        };

        const activePanel = () => root.querySelector(`[data-ppdb-liftoff-panel="${root.dataset.activeAudience}"]`);

        const rebuildActiveStoryline = () => {
          const panel = activePanel();
          if (!panel || panel.hidden) return;

          buildStoryline(panel);
          updateStoryline(panel);
        };

        const queueRebuild = () => {
          if (rebuildFrame) window.cancelAnimationFrame(rebuildFrame);
          rebuildFrame = window.requestAnimationFrame(() => {
            rebuildFrame = 0;
            rebuildActiveStoryline();
          });
        };

        const activate = (audience) => {
          const targetPanel = root.querySelector(`[data-ppdb-liftoff-panel="${audience}"]`);
          if (!targetPanel) return;

          root.dataset.activeAudience = audience;
          tabs.forEach((tab) => {
            const isActive = tab.dataset.ppdbLiftoffTab === audience;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
          });
          panels.forEach((panel) => { panel.hidden = panel.dataset.ppdbLiftoffPanel !== audience; });
          window.requestAnimationFrame(() => {
            revealVisibleItems(targetPanel);
            buildStoryline(targetPanel);
            updateStoryline(targetPanel);
          });
        };

        tabs.forEach((tab) => tab.addEventListener('click', () => {
          if (!tab.disabled) activate(tab.dataset.ppdbLiftoffTab);
        }));

        let ticking = false;
        const queueUpdate = () => {
          if (ticking) return;
          ticking = true;
          window.requestAnimationFrame(() => {
            revealVisibleItems();
            const panel = activePanel();
            if (panel && !panel.hidden) updateStoryline(panel);
            ticking = false;
          });
        };

        window.addEventListener('scroll', queueUpdate, { passive: true });
        window.addEventListener('resize', queueRebuild);

        if ('ResizeObserver' in window) {
          const resizeObserver = new ResizeObserver(queueRebuild);
          panels.forEach((panel) => {
            const stack = panel.querySelector('.ppdb-liftoff__stack');
            if (stack) resizeObserver.observe(stack);
          });
        }

        window.requestAnimationFrame(() => {
          revealVisibleItems();
          rebuildActiveStoryline();
        });
