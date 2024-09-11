define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.hand', null, {
        constructor() {
        },

        addHand() {
            dojo.place(this.format_block('jstpl_hand', {}), 'board');
            this.gamedatas.players[this.player_id].hand.forEach((location) => {
                this.addLocation(location, $('hand'));
            });
        },
    });
});
