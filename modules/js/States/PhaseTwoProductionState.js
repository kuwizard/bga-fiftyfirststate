define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseTwoProduction', null, {
        constructor() {
            this._notifications.push(['playerPhaseTwoProduction', 500]);
        },

        notif_playerPhaseTwoProduction(n) {
            debug('Notif: playerPhaseTwoProduction', n);
        },
    });
});
