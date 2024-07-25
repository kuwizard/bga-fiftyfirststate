define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.playerboard', null, {
        constructor() {
            // this._notifications.push(['moneyChanged', 1]);
        },

        markPassed(players) {
            Object.values(players).forEach((player) => {
                if (player.passed) {
                    dojo.addClass(`overall_player_board_${player.id}`, 'passed');
                }
            })
        },
    });
});
