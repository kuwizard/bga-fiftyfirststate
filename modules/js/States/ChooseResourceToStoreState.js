define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.chooseResourceToStore', null, {
        constructor() {

        },

        onEnteringStateChooseResourceToStore(args) {
            debug('ChooseResourceToStore state', args);
            setTimeout(() => { // Weird BGA bug, buttons do not appear
                if (args._private) {
                    args._private.forEach((resource) => {
                        this.addActionButtonWithResource(resource, 'actChooseResourceToStore');
                    });
                    this.addPrimaryActionButton(
                        'buttonActionPassStoring',
                        _('Done'),
                        () => this.takeAction('actPassStoringResource')
                    );
                }
                this.addResetTurnButton()
            }, 1);
        },
    });
});
