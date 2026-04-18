define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.placeDefence', null, {
        constructor() {
            this._notifications.push(['locationDefended', 1]);
        },

        onEnteringStatePlaceDefence(args) {
            debug('Place Defence state', args);
            if (this.isCurrentPlayerActive()) {
                this.makeLocationsUnselectable('.location');
                this.makeLocationsUnselectable('.connection');
                this.makeLocationsSelectableAndClickable(
                    `#faction_${this.player_id} .location`,
                    'actPlaceDefence',
                    args.locations
                );
                this.addUndoButton();
            }
        },

        notif_locationDefended(n) {
            debug('Notif: locationDefended', n);
            this.placeResourcesOnLocation(n.args.location.id, ['defence']);
        },
    });
});
