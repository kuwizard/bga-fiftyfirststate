define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseOneLookoutChoose', null, {
        constructor() {
        },

        onEnteringStatePhaseOneLookoutChoose(args) {
            debug('phaseOneLookoutChoose state', args);
            dojo.style('lookout', 'display', 'flex');
            dojo.style('connections', 'display', 'none');
            this.destroyAll('#lookout .location');
            args.forEach((location) => {
                const locationElement = dojo.place(this.format_block(
                    'jstpl_location',
                    this.enrichLocationObject(location)
                ), 'lookout');
                if (this.isCurrentPlayerActive()) {
                    this.addSelectableClass(locationElement);
                    this.dojoConnect(
                        locationElement,
                        () => this.clickLocationLookout(this.extractId(locationElement, 'location'))
                    );
                }
            });
        },

        onLeavingStatePhaseOneLookoutChoose() {
            dojo.style('lookout', 'display', 'none');
            dojo.style('connections', 'display', 'flex');
        },

        clickLocationLookout(id) {
            this.takeAction('actChooseCardLookout', { id: id })
        },
    });
});
