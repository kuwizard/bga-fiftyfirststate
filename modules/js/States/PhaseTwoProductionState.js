define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseTwoProduction', null, {
        constructor() {
            this._notifications.push(['playerPhaseTwoProduction', 500]);
        },

        async onEnteringStatePhaseTwoProduction() {
            // Waiting until discard from phase one will be done
            await this.waitForDisappearance('.discarding');
            // ...and waiting until card will be picked up from the deck
            await this.waitForDisappearance('.moving');
            dojo.addClass('lookout', 'hidden');
            dojo.removeClass('connections', 'hidden');
        },

        notif_playerPhaseTwoProduction(n) {
            debug('Notif: playerPhaseTwoProduction', n);
        },
    });
});
