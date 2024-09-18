define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.lexemes', null, {
        placeText(tpl, text, destination, value = null) {
            dojo.place(this.format_block(tpl, { text: text, value: value }), destination);
        },

        getDeckLexeme() {
            return _('Deck');
        },

        getDiscardLexeme() {
            return _('Discard');
        },

        getExpandConnectionsLexeme() {
            return _('Expand this to see Connections');
        },

        getExpandLocationsLexeme() {
            return _('Expand this to see locations to choose from');
        },

        getSelectorAllLexeme() {
            return _('All');
        },

        getSelectorLocationsLexeme() {
            return _('Locations');
        },

        getSelectorConnectionsLexeme() {
            return _('Connections');
        },
    });
});
