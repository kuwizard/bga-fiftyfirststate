define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.factionboard', null, {
        constructor() {
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

                Object.keys(player.usedFactionActions).forEach((order) => {
                    const spentArea = this.querySingle(`#faction_${player.id} .spent[data-order="${order}"]`);
                    player.usedFactionActions[order].forEach((resource) => {
                        this.placeResourceOnFactionAction(spentArea, resource);
                    })
                })
            });
        },

        placeResourceOnFactionAction(element, resource) {
            const res = dojo.place(this.format_block('jstpl_resource_icon', { type: resource }), element);
            const getRandomNumber = (min, max) => {
                return Math.floor(Math.random() * (max - min) + min);
            }
            dojo.style(res, 'margin-left', `${getRandomNumber(-7, 7)}px`);
            dojo.style(res, 'margin-top', `${getRandomNumber(-7, 7)}px`);
            dojo.style(res, 'transform', `rotate(${getRandomNumber(-20, 20)}deg)`);
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
