define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.activateProduction', null, {
        constructor() {
        },

        onEnteringStateActivateProduction(args) {
            debug('Activate Production state', args);
            if (this.isCurrentPlayerActive()) {
                this.makeLocationsUnselectable('.location');
                this.makeLocationsUnselectable('.connection');
                this.makeLocationsSelectableAndClickable(
                    `#faction_${this.player_id} .location`,
                    'actActivateProduction',
                    args.locations
                );
                this.addUndoButton();
            }
        },
    });
});
