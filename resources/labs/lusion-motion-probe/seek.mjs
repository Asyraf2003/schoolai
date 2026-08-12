export async function waitFrames(page, count = 2) {
  await page.evaluate((frames) => new Promise((resolve) => {
    const tick = (remaining) => requestAnimationFrame(() => (
      remaining > 1 ? tick(remaining - 1) : resolve()
    ));
    tick(frames);
  }), count);
}

export async function seekEntryCorridor(page, locator, options) {
  const { height, maxDelta = 420, maxSteps = 80 } = options;
  const targetY = height * 0.82;
  let cumulativeWheelY = 0;

  for (let step = 0; step < maxSteps; step += 1) {
    const rect = await locator.boundingBox();
    if (!rect) throw new Error('Probe node disappeared while seeking');
    const distance = rect.y - targetY;
    if (Math.abs(distance) <= height * 0.12) {
      await waitFrames(page, 4);
      const settled = await locator.boundingBox();
      return { steps: step, cumulativeWheelY, rect: settled };
    }

    const direction = Math.sign(distance) || 1;
    const magnitude = Math.min(maxDelta, Math.max(70, Math.abs(distance) * 0.16));
    const deltaY = direction * magnitude;
    await page.mouse.wheel(0, deltaY);
    cumulativeWheelY += deltaY;
    await waitFrames(page, 4);
  }

  const rect = await locator.boundingBox();
  throw new Error(`Unable to seek probe into entry corridor; finalY=${rect?.y}`);
}
