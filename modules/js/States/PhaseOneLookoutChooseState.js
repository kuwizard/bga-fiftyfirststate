define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseOneLookoutChoose', null, {
        constructor() {
        },

        onEnteringStatePhaseOneLookoutChoose(args) {
            debug('phaseOneLookoutChoose state', args);
            dojo.style('lookoutBlock', 'display', 'flex');
            this.destroyAll('#lookoutBlock .location');
            args.forEach((location) => {
                const locationElement = dojo.place(this.format_block('jstpl_location', location), 'lookoutBlock');
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
            dojo.style('lookoutBlock', 'display', 'none');
        },

        addLookoutElement() {
            dojo.place(this.format_block('jstpl_lookout', {}), 'board');
        },

        clickLocationLookout(id) {
            this.takeAction('actChooseCardLookout', { id: id })
        },
    });
});
