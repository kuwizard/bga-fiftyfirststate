define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseOneLookoutChoose', null, {
        constructor() {
        },

        onEnteringStatePhaseOneLookoutChoose(args) {
            debug('phaseOneLookoutChoose state', args);
            if (this.isCurrentPlayerActive()) {
                args.forEach((id) => {
                    this.addPrimaryActionButton(
                        'buttonDiscardCards' + id,
                        (id),
                        () => this.takeAction('actChooseCardLookout', { id: id })
                    );
                });
            }
        },
    });
});
