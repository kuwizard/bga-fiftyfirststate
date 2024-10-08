define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.develop', null, {
        constructor() {
        },

        onEnteringStateDevelopChooseFromHand(args) {
            if (this.isCurrentPlayerActive()) {
                this.makeLocationsUnselectable('.factionBoard .location');
                this.makeLocationsUnselectable('#connections .connection');
                this.makeLocationsSelectableAndClickable(
                    '#handLocations .location',
                    'actDevelopChooseFromHand',
                    args.possibleHandIds
                );
                this.makeLocationsUnselectable('#discard .location');
                this.addUndoButton();
            }
        },

        onEnteringStateDevelopChooseDestination(args) {
            if (this.isCurrentPlayerActive()) {
                this.makeLocationsUnselectable(`#handLocations .location:not(#location_${args.newLocationId})`);
                this.makeLocationsUnselectable(`.factionBoard:not(#faction_${this.player_id}) .location`);
                this.addSelectedClass(`location_${args.newLocationId}`);
                this.makeLocationsSelectableAndClickable(
                    `#faction_${this.player_id} .location`,
                    'actDevelopChooseDestination',
                    args.possibleDestinations,
                );
                this.makeLocationsUnselectable('#discard .location');
                this.makeLocationsUnselectable('.connection');
                this.addUndoButton();
            }
        },
    });
});
