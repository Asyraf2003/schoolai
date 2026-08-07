const clamp = (value, min = 0, max = 1) => Math.max(min, Math.min(max, value));
const lerp = (from, to, progress) => from + ((to - from) * progress);

const profiles = {
  wide: {
    width: 1920,
    height: 965,
    mission: [
      [0, { x: 120, y: 250, w: 520, h: 600 }],
      [.15, { x: -400, y: 250, w: 520, h: 600 }],
      [.45, { x: -760, y: 250, w: 520, h: 600 }],
    ],
    main: [
      [0, { x: 670, y: 180, w: 1210, h: 785 }],
      [.15, { x: 200, y: 120, w: 1300, h: 845 }],
      [.30, { x: 200, y: 120, w: 1300, h: 845 }],
      [.45, { x: 0, y: 0, w: 1920, h: 965 }],
      [.60, { x: -1920, y: 0, w: 1920, h: 965 }],
    ],
    thumbOne: [
      [0, { x: 1540, y: 330, w: 340, h: 450 }],
      [.15, { x: 1200, y: 250, w: 500, h: 450 }],
      [.30, { x: 1200, y: -200, w: 500, h: 450 }],
      [.45, { x: 1200, y: -650, w: 500, h: 450 }],
      [.60, { x: -720, y: -650, w: 500, h: 450 }],
    ],
    thumbTwo: [
      [0, { x: 1540, y: 965, w: 340, h: 450 }],
      [.15, { x: 1200, y: 965, w: 500, h: 450 }],
      [.30, { x: 1200, y: 250, w: 500, h: 450 }],
      [.45, { x: 710, y: 250, w: 500, h: 465 }],
      [.60, { x: -1210, y: 250, w: 500, h: 465 }],
    ],
    title: [
      [.45, { x: 1920, y: 300, w: 1280, h: 350 }],
      [.60, { x: 320, y: 300, w: 1280, h: 350 }],
    ],
    copy: [
      [.45, { x: 1920, y: 680, w: 580, h: 120 }],
      [.60, { x: 320, y: 680, w: 580, h: 120 }],
    ],
  },
  mid: {
    width: 768,
    height: 1024,
    mission: [[0, { x: 42, y: 200, w: 300, h: 590 }], [.15, { x: -300, y: 200, w: 300, h: 590 }], [.45, { x: -500, y: 200, w: 300, h: 590 }]],
    main: [[0, { x: 300, y: 170, w: 468, h: 854 }], [.15, { x: 80, y: 120, w: 610, h: 904 }], [.30, { x: 80, y: 120, w: 610, h: 904 }], [.45, { x: 0, y: 0, w: 768, h: 1024 }], [.60, { x: -768, y: 0, w: 768, h: 1024 }]],
    thumbOne: [[0, { x: 560, y: 330, w: 180, h: 300 }], [.15, { x: 470, y: 250, w: 240, h: 330 }], [.30, { x: 470, y: -240, w: 240, h: 330 }], [.45, { x: 470, y: -570, w: 240, h: 330 }], [.60, { x: -298, y: -570, w: 240, h: 330 }]],
    thumbTwo: [[0, { x: 560, y: 1024, w: 180, h: 300 }], [.15, { x: 470, y: 1024, w: 240, h: 330 }], [.30, { x: 470, y: 250, w: 240, h: 330 }], [.45, { x: 250, y: 300, w: 268, h: 380 }], [.60, { x: -518, y: 300, w: 268, h: 380 }]],
    title: [[.45, { x: 768, y: 220, w: 628, h: 360 }], [.60, { x: 70, y: 220, w: 628, h: 360 }]],
    copy: [[.45, { x: 768, y: 650, w: 500, h: 160 }], [.60, { x: 70, y: 650, w: 500, h: 160 }]],
  },
  compact: {
    width: 360,
    height: 800,
    mission: [[0, { x: 24, y: 140, w: 312, h: 460 }], [.15, { x: -300, y: 140, w: 312, h: 460 }], [.45, { x: -420, y: 140, w: 312, h: 460 }]],
    main: [[0, { x: 52, y: 390, w: 308, h: 410 }], [.15, { x: 12, y: 160, w: 336, h: 640 }], [.30, { x: 12, y: 160, w: 336, h: 640 }], [.45, { x: 0, y: 0, w: 360, h: 800 }], [.60, { x: -360, y: 0, w: 360, h: 800 }]],
    thumbOne: [[0, { x: 225, y: 500, w: 120, h: 180 }], [.15, { x: 178, y: 260, w: 160, h: 220 }], [.30, { x: 178, y: -220, w: 160, h: 220 }], [.45, { x: 178, y: -440, w: 160, h: 220 }], [.60, { x: -182, y: -440, w: 160, h: 220 }]],
    thumbTwo: [[0, { x: 225, y: 800, w: 120, h: 180 }], [.15, { x: 178, y: 800, w: 160, h: 220 }], [.30, { x: 178, y: 260, w: 160, h: 220 }], [.45, { x: 70, y: 260, w: 220, h: 300 }], [.60, { x: -290, y: 260, w: 220, h: 300 }]],
    title: [[.45, { x: 360, y: 150, w: 312, h: 340 }], [.60, { x: 24, y: 150, w: 312, h: 340 }]],
    copy: [[.45, { x: 360, y: 560, w: 300, h: 140 }], [.60, { x: 24, y: 560, w: 300, h: 140 }]],
  },
};

function interpolateBox(points, progress) {
  if (progress <= points[0][0]) return { ...points[0][1] };
  const last = points[points.length - 1];
  if (progress >= last[0]) return { ...last[1] };

  for (let index = 0; index < points.length - 1; index += 1) {
    const [fromAt, from] = points[index];
    const [toAt, to] = points[index + 1];
    if (progress < fromAt || progress > toAt) continue;
    const local = clamp((progress - fromAt) / Math.max(.0001, toAt - fromAt));
    return {
      x: lerp(from.x, to.x, local),
      y: lerp(from.y, to.y, local),
      w: lerp(from.w, to.w, local),
      h: lerp(from.h, to.h, local),
    };
  }
  return { ...last[1] };
}

function scaledBox(points, profile, progress, viewport, rtl) {
  const box = interpolateBox(points, progress);
  const scaled = {
    x: box.x * (viewport.width / profile.width),
    y: box.y * (viewport.height / profile.height),
    w: box.w * (viewport.width / profile.width),
    h: box.h * (viewport.height / profile.height),
  };
  if (rtl) scaled.x = viewport.width - (scaled.x + scaled.w);
  return scaled;
}

function fade(progress, from, to) {
  return clamp((progress - from) / Math.max(.0001, to - from));
}

function programFrames(progress, frameCount) {
  const opacities = Array(frameCount).fill(0);
  if (!frameCount || progress < .75) return { opacities, activeIndex: -1 };

  const loop = clamp((progress - .75) / .25);
  if (loop >= 1) {
    opacities[frameCount - 1] = 1;
    return { opacities, activeIndex: frameCount - 1 };
  }

  const scaled = loop * frameCount;
  const index = Math.min(frameCount - 1, Math.floor(scaled));
  const local = scaled - index;
  const next = Math.min(frameCount - 1, index + 1);
  const mix = index === next ? 0 : fade(local, .62, 1);
  opacities[index] = 1 - mix;
  opacities[next] = Math.max(opacities[next], mix);
  return { opacities, activeIndex: mix >= .5 ? next : index };
}

export function createProgramGeometry(root) {
  let start = 0;
  let travel = 1;
  let viewport = { width: window.innerWidth, height: window.innerHeight };
  let profile = profiles.wide;
  let rtl = document.documentElement.dir === 'rtl';

  function chooseProfile() {
    if (viewport.width < 768) return profiles.compact;
    if (viewport.width < 1181) return profiles.mid;
    return profiles.wide;
  }

  function measure() {
    viewport = {
      width: Math.max(1, window.innerWidth),
      height: Math.max(1, window.innerHeight),
    };
    rtl = document.documentElement.dir === 'rtl';
    profile = chooseProfile();
    const travelScreens = viewport.width >= 1181 ? 9 : viewport.width >= 768 ? 8.5 : 8;
    travel = viewport.height * travelScreens;
    root.style.height = `${viewport.height + travel}px`;
    start = window.scrollY + root.getBoundingClientRect().top;
  }

  function readTarget() {
    return clamp((window.scrollY - start) / travel);
  }

  function state(progress, frameCount) {
    const current = clamp(progress);
    const frames = programFrames(current, frameCount);
    const textBlend = fade(current, .70, .75);
    const textTone = Math.round(17 + (238 * textBlend));

    return {
      progress: current,
      boxes: {
        mission: scaledBox(profile.mission, profile, current, viewport, rtl),
        main: scaledBox(profile.main, profile, current, viewport, rtl),
        thumbOne: scaledBox(profile.thumbOne, profile, current, viewport, rtl),
        thumbTwo: scaledBox(profile.thumbTwo, profile, current, viewport, rtl),
        title: scaledBox(profile.title, profile, current, viewport, rtl),
        copy: scaledBox(profile.copy, profile, current, viewport, rtl),
      },
      missionOpacity: 1 - fade(current, .34, .45),
      thumbOneOpacity: 1 - fade(current, .30, .40),
      handoffOpacity: 1 - fade(current, .56, .60),
      showcaseOpacity: fade(current, .45, .60),
      backgroundsOpacity: fade(current, .72, .75),
      railOpacity: fade(current, .75, .78),
      textColor: `rgb(${textTone} ${textTone} ${textTone})`,
      frameOpacities: frames.opacities,
      activeIndex: frames.activeIndex,
    };
  }

  return { measure, readTarget, state };
}
