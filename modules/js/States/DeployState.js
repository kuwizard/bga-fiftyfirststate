define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.deploy', null, {
        constructor() {
        },

        onEnteringStateDeployChooseFromHand(args) {
            if (this.isCurrentPlayerActive()) {
                this.makeLocationsUnselectable('.factionBoard .location');
                this.makeLocationsUnselectable('#connections .connection');
                this.makeLocationsSelectableAndClickable(
                    '#hand .location',
                    'actDeployChooseFromHand',
                    args.possibleHandIds
                );
                this.addUndoButton();
            }
        },

        onEnteringStateDeployChooseDestination(args) {
            if (this.isCurrentPlayerActive()) {
                this.makeLocationsUnselectable(`#hand .location:not(#location_${args.newLocationId})`);
                this.makeLocationsUnselectable(`.factionBoard:not(#faction_${this.player_id}) .location`);
                this.addSelectedClass(`location_${args.newLocationId}`);
                this.makeLocationsSelectableAndClickable(
                    `#faction_${this.player_id} .location`,
                    'actDeployChooseDestination',
                    args.possibleDestinationIds
                );
                this.addUndoButton();
            }
        },
    });
});
