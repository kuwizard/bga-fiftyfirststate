define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.factionboard', null, {
        constructor() {
            // this._notifications.push(['resourcesChanged', 1]);
        },

        addBoard() {
            dojo.place(this.format_block('jstpl_board', {}), 'game_play_area');
        },

        addFactionBoards() {
            this.forEachPlayer((player) => {
                const factionBoard = dojo.place(this.format_block('jstpl_faction_board', player), 'board');
                ['production', 'feature', 'actions'].forEach((row) => {
                    const rowElement = factionBoard.querySelector(`.${row}`);
                    player.locations[row].forEach((card) => {
                        dojo.place(this.format_block('jstpl_location', card), rowElement);
                    });
                });

            });
        },


        //
        // notif_resourcesChanged(n) {
        //     debug('Notif: resourcesChanged', n);
        //     const data = n.args.resources;
        //     Object.keys(data).forEach((resource) => {
        //         this.querySingle(`#player_board_${n.args.player_id} .${resource}Value`).innerText = data[resource];
        //     });
        // },
    });
});
