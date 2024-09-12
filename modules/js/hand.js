define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.hand', null, {
        constructor() {
        },

        addHand() {
            dojo.place(this.format_block('jstpl_hand', {}), 'board');
            const elements = this.gamedatas.players[this.player_id].hand.map((location) => {
                return this.addLocation(location, $('hand'));
            });

            this.setMagicLocationClasses(elements);
        },
    });
});
