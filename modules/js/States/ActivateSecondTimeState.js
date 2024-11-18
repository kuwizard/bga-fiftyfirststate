define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.activateSecondTime', null, {
        constructor() {
        },

        onEnteringStateActivateSecondTime(args) {
            debug('Activate Second Time state', args);
            this.activateSecondTimePreparation(args.locationId);
        },

        onEnteringStateActivateSpendWorkersAgain(args) {
            debug('Activate Spend Workers Second Time state', args);
            this.activateSecondTimePreparation();
        },

        activateSecondTimePreparation(locationId = null) {
            if (this.isCurrentPlayerActive()) {
                if (locationId !== null) {
                    this.addSelectedClass(`location_${locationId}`);
                    this.dojoConnect(`location_${locationId}`, () => this.takeAction('actActivateAgain', {}));
                }
                this.addPrimaryActionButton('buttonYes', _('Yes, activate again'),
                    () => this.takeAction('actActivateAgain', {})
                );
                this.addPrimaryActionButton('buttonNo', _('No'),
                    () => this.takeAction('actDoNotActivateAgain', {})
                );
            }
        },
    });
});
