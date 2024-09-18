define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.hand', null, {
        constructor() {
        },

        addHand() {
            dojo.place(this.format_block('jstpl_hand', {}), 'board');
            dojo.place(this.format_block('jstpl_selector', {}), 'hand', 'first');
            ['unselected', 'selected'].forEach((selectorBlock) => {
                this.placeText(
                    'jstpl_collapsed_text',
                    this.getSelectorAllLexeme(),
                    this.querySingle(`#handSelector #${selectorBlock} .allBlock`)
                );
                this.placeText(
                    'jstpl_collapsed_text',
                    this.getSelectorLocationsLexeme(),
                    this.querySingle(`#handSelector #${selectorBlock} .locationsBlock`)
                );
                this.placeText(
                    'jstpl_collapsed_text',
                    this.getSelectorConnectionsLexeme(),
                    this.querySingle(`#handSelector #${selectorBlock} .connectionsBlock`)
                );
            });

            this.getAllSelectorOptions().forEach((option) => {
                dojo.connect(this.querySingle(`#handSelector #clickArea .${option}Block`), 'click', () => {
                    const hand = this.querySingle('#hand');
                    this.getAllSelectorOptions().forEach((selector) => {
                        dojo.removeClass(hand, selector);
                    });
                    dojo.addClass(hand, option);
                });
            });
            this.gamedatas.players[this.player_id].hand.forEach((location) => {
                this.addLocation(location, $('handLocations'));
            });
            this.gamedatas.players[this.player_id].connections.forEach((connection) => {
                this.addConnection(connection, 'handConnections')
            });
        },

        getAllSelectorOptions() {
            return ['all', 'locations', 'connections'];
        },
    });
});
