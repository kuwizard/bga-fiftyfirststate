define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.playerboard', null, {
        constructor() {
            this._notifications.push(['resourcesChanged', 1]);
        },

        markPassed(players) {
            Object.values(players).forEach((player) => {
                if (player.passed) {
                    dojo.addClass(`overall_player_board_${player.id}`, 'passed');
                }
            })
        },

        addResourcesTable() {
            this.forEachPlayer((player) => {
                dojo.place(this.format_block('jstpl_player_board', player), 'player_board_' + player.id);
            });
        },

        notif_resourcesChanged(n) {
            debug('Notif: resourcesChanged', n);
            const data = n.args.resources;
            Object.keys(data).forEach((resource) => {
                if (resource === 'score') {
                    this.scoreCtrl[n.args.player_id].toValue(data[resource]);
                }
                this.querySingle(`#player_board_${n.args.player_id} .${resource}Value`).innerText = data[resource];
            });
        },
    });
});
