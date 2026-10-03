        const buildStoryline = (panel) => {
          if (!panel || panel.hidden) return;

          const mount = panel.querySelector('[data-ppdb-storyline]');
          const stack = panel.querySelector('.ppdb-liftoff__stack');
          const nodes = Array.from(panel.querySelectorAll('[data-ppdb-story-node]'));
          if (!mount || !stack || !nodes.length) return;

          const stackRect = stack.getBoundingClientRect();
          const width = Math.max(stack.clientWidth, 1);
          const height = Math.max(stack.scrollHeight, stack.clientHeight, 1);
          const gradientId = `ppdbStoryRainbow-${panel.dataset.ppdbLiftoffPanel || 'panel'}-${storylineSequence += 1}`;
          const svg = createSvgElement('svg');
          svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
          svg.setAttribute('preserveAspectRatio', 'none');
          svg.setAttribute('focusable', 'false');

          const defs = createSvgElement('defs');
          const gradient = createSvgElement('linearGradient');
          gradient.setAttribute('id', gradientId);
          gradient.setAttribute('gradientUnits', 'userSpaceOnUse');
          gradient.setAttribute('x1', '0');
          gradient.setAttribute('y1', '0');
          gradient.setAttribute('x2', String(width));
          gradient.setAttribute('y2', String(height));

          [
            ['0%', '#ff4d8d'],
            ['16%', '#ff8a3d'],
            ['32%', '#ffd84d'],
            ['48%', '#57d68d'],
            ['64%', '#4cc9f0'],
            ['80%', '#6c63ff'],
            ['92%', '#c77dff'],
            ['100%', '#ff4d8d'],
          ].forEach(([offset, color]) => {
            const stop = createSvgElement('stop');
            stop.setAttribute('offset', offset);
            stop.setAttribute('stop-color', color);
            gradient.appendChild(stop);
          });

          defs.appendChild(gradient);
          svg.appendChild(defs);

          const stages = [];
          const glowStages = [];
          let previousStageEnd = 0;

          nodes.forEach((node, index) => {
            const rect = node.getBoundingClientRect();
            const x = rect.left - stackRect.left;
            const y = rect.top - stackRect.top;
            const frameInset = 3;
            const frameWidth = Math.max(rect.width - (frameInset * 2), 1);
            const frameHeight = Math.max(rect.height - (frameInset * 2), 1);
            const radius = Math.min(34, Math.max(22, Math.min(frameWidth, frameHeight) * 0.12));
            const frame = createSvgElement('path');
            frame.setAttribute('class', 'ppdb-liftoff-story-path ppdb-liftoff-story-frame');
            frame.setAttribute('stroke', `url(#${gradientId})`);
            frame.setAttribute('d', roundedFramePath(x + frameInset, y + frameInset, frameWidth, frameHeight, radius));
            svg.appendChild(frame);

            const frameStart = index === 0
              ? Math.max(y - 70, 0)
              : previousStageEnd;
            const frameDuration = clamp(frameHeight * 0.92, 150, 280);
            const frameEnd = frameStart + frameDuration;
            const glowHold = clamp(frameHeight * 0.46, 120, 190);
            const glowEnd = frameEnd + glowHold;
            stages.push(preparePath(frame, frameStart, frameEnd));
            glowStages.push({ node, frame, start: frameEnd, holdEnd: glowEnd });
            previousStageEnd = glowEnd;

            if (index >= nodes.length - 1) return;

            const nextNode = nodes[index + 1];
            const nextRect = nextNode.getBoundingClientRect();
            const nextX = nextRect.left - stackRect.left;
            const nextY = nextRect.top - stackRect.top;
            const nextCard = nextNode.closest('.ppdb-liftoff-card');
            const nextVisual = nextCard?.querySelector('.ppdb-liftoff-card__visual');
            const nextVisualRect = nextVisual?.getBoundingClientRect();
            const nextTextCenterX = nextX + (nextRect.width / 2);
            const visualCenterX = nextVisualRect
              ? (nextVisualRect.left - stackRect.left) + (nextVisualRect.width / 2)
              : (x + (rect.width / 2) + nextTextCenterX) / 2;
            const visualCenterY = nextVisualRect
              ? (nextVisualRect.top - stackRect.top) + (nextVisualRect.height * 0.5)
              : y + rect.height + ((nextY - (y + rect.height)) * 0.56);
            const visualHeight = nextVisualRect?.height ?? Math.max(nextY - y, 220);
            const horizontalRelation = visualCenterX - nextTextCenterX;
            const isRtl = document.documentElement.dir === 'rtl';
            const enterFromLeft = Math.abs(horizontalRelation) > 24
              ? horizontalRelation < 0
              : ((index + (isRtl ? 1 : 0)) % 2 === 0);
            const endX = enterFromLeft
              ? nextX + frameInset
              : nextX + nextRect.width - frameInset;
            const endY = nextY + (nextRect.height * 0.52);
            const connector = createSvgElement('path');
            connector.setAttribute('class', 'ppdb-liftoff-story-path ppdb-liftoff-story-connector');
            connector.setAttribute('stroke', `url(#${gradientId})`);
            connector.setAttribute('d', connectorPath({
              startX: x + (rect.width / 2),
              startY: y + rect.height - frameInset,
              endX,
              endY,
              viaX: visualCenterX,
              viaY: visualCenterY,
              visualHeight,
            }));
            svg.appendChild(connector);

            const connectorStart = previousStageEnd;
            const connectorDuration = clamp((nextY - y) * 0.5, 230, 460);
            const connectorEnd = Math.max(nextY - 84, connectorStart + connectorDuration);
            stages.push(preparePath(connector, connectorStart, connectorEnd));
            previousStageEnd = connectorEnd;
          });

          mount.replaceChildren(svg);
          storylineState.set(panel, { stages, glowStages });
        };
