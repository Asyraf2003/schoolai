export async function waitFrames(page, count = 2) {
  await page.evaluate((frames) => new Promise((resolve) => {
    const tick = (remaining) => requestAnimationFrame(() => (
      remaining > 1 ? tick(remaining - 1) : resolve()
    ));
    tick(frames);
  }), count);
}

async function waitForVisualSettle(page, locator, options = {}) {
  const {
    maxFrames = 120,
    epsilon = 0.35,
    stableFrames = 6,
  } = options;

  let rect = await locator.boundingBox();
  if (!rect) throw new Error('Probe node disappeared while settling');

  let stable = 0;
  for (let frame = 1; frame <= maxFrames; frame += 1) {
    await waitFrames(page, 1);
    const next = await locator.boundingBox();
    if (!next) throw new Error('Probe node disappeared while settling');

    const delta = Math.abs(next.y - rect.y);
    stable = delta <= epsilon ? stable + 1 : 0;
    rect = next;

    if (stable >= stableFrames) {
      return { rect, frames: frame, settled: true };
    }
  }

  return { rect, frames: maxFrames, settled: false };
}

export async function seekEntryCorridor(page, locator, options) {
  const { height, maxDelta = 420, maxSteps = 80 } = options;
  const targetY = height * 0.82;
  const tolerance = height * 0.12;
  let cumulativeWheelY = 0;
  let settleFrames = 0;

  for (let step = 0; step < maxSteps; step += 1) {
    const rect = await locator.boundingBox();
    if (!rect) throw new Error('Probe node disappeared while seeking');

    const distance = rect.y - targetY;
    if (Math.abs(distance) <= tolerance) {
      const settled = await waitForVisualSettle(page, locator);
      settleFrames += settled.frames;
      const finalDistance = settled.rect.y - targetY;
      if (Math.abs(finalDistance) <= tolerance) {
        return {
          steps: step,
          cumulativeWheelY,
          settleFrames,
          rect: settled.rect,
        };
      }
    }

    const direction = Math.sign(distance) || 1;
    const magnitude = Math.min(maxDelta, Math.max(70, Math.abs(distance) * 0.16));
    const deltaY = direction * magnitude;
    await page.mouse.wheel(0, deltaY);
    cumulativeWheelY += deltaY;

    const settled = await waitForVisualSettle(page, locator);
    settleFrames += settled.frames;
  }

  const rect = await locator.boundingBox();
  throw new Error(
    `Unable to seek probe into entry corridor; finalY=${rect?.y}; `
    + `wheelY=${cumulativeWheelY}; settleFrames=${settleFrames}`,
  );
}
