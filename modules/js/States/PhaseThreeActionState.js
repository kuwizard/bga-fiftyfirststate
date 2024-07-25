define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseThreeAction', null, {
        constructor() {
        },

        onEnteringStatePhaseThreeAction(args) {
            debug('phase Three Action state', args);
            if (this.isCurrentPlayerActive()) {
                this.addPrimaryActionButton(
                    'buttonDoSomething',
                    _('Do something'),
                    () => this.takeAction('actDoSomething', {})
                );
                this.addPrimaryActionButton(
                    'buttonActionPass',
                    _('Pass'),
                    () => this.takeAction('actActionPass', {})
                );
            }
        },
    });
});
