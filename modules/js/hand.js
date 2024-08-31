define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.hand', null, {
        constructor() {
            // this._notifications.push(['resourcesChanged', 1]);
        },

        addHand() {
            dojo.place(this.format_block('jstpl_hand', {}), 'board');
            this.gamedatas.players[this.player_id].hand.forEach((location) => {
                dojo.place(this.format_block('jstpl_location', this.enrichLocationObject(location)), 'hand');
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
