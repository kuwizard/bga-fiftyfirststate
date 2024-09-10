define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.activateSecondTime', null, {
        constructor() {
        },

        onEnteringStateActivateSecondTime(args) {
            debug('Activate Second Time state', args);
            if (this.isCurrentPlayerActive()) {
                this.addSelectedClass(`location_${args.locationId}`);
                this.dojoConnect(`location_${args.locationId}`, () => this.takeAction('actActivateAgain', {}));
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
