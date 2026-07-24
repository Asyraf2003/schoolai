    /*
     * The story deliberately spends more scroll distance on each transition.
     * Each statistic now enters slowly, settles for a beat, breathes once, and
     * only then leaves before the About panel travels to the opposite side.
     */
var TIMING = {
        intro: 1.05,
        firstShift: 1.4,
        preStat: 0.32,
        statEnter: 1.25,
        statSettle: 0.48,
        statHold: 1.05,
        statExit: 0.95,
        gapAfterExit: 0.34,
        aboutShift: 1.4,
        preNextStat: 0.34,
        finalPause: 0.5,
        outro: 0.72
    };

export function buildTimeline(statCount) {
        var stats = [];
        var shifts = [];
        var introEnd = TIMING.intro;
        var firstShiftEnd = introEnd + TIMING.firstShift;
        var cursor = firstShiftEnd + TIMING.preStat;

        shifts.push({
            start: introEnd,
            end: firstShiftEnd,
            fromIndex: -1,
            toIndex: 0
        });

        for (var index = 0; index < statCount; index += 1) {
            var enterStart = cursor;
            var enterEnd = enterStart + TIMING.statEnter;
            var settleEnd = enterEnd + TIMING.statSettle;
            var holdEnd = settleEnd + TIMING.statHold;
            var exitEnd = holdEnd + TIMING.statExit;

            stats.push({
                enterStart: enterStart,
                enterEnd: enterEnd,
                settleEnd: settleEnd,
                holdEnd: holdEnd,
                exitEnd: exitEnd
            });

            cursor = exitEnd;

            if (index < statCount - 1) {
                var shiftStart = cursor + TIMING.gapAfterExit;
                var shiftEnd = shiftStart + TIMING.aboutShift;

                shifts.push({
                    start: shiftStart,
                    end: shiftEnd,
                    fromIndex: index,
                    toIndex: index + 1
                });

                cursor = shiftEnd + TIMING.preNextStat;
            }
        }

        var lastExit = stats.length
            ? stats[stats.length - 1].exitEnd
            : firstShiftEnd;
        var outroStart = lastExit + TIMING.finalPause;
        var total = outroStart + TIMING.outro;

        return {
            introEnd: introEnd,
            firstShiftEnd: firstShiftEnd,
            firstStatStart: stats.length ? stats[0].enterStart : firstShiftEnd,
            lastExit: lastExit,
            outroStart: outroStart,
            total: total,
            stats: stats,
            shifts: shifts
        };
    }
