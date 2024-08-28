define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.chooseResourceToStore', null, {
        constructor() {

        },

        onEnteringStateChooseResourceToStore(args) {
            debug('ChooseResourceToStore state', args);
            setTimeout(() => { // Weird BGA bug, buttons do not appear
                if (args._private) {
                    args._private.forEach((resource) => {
                        this.addPrimaryActionButton(
                            `buttonStore${resource}`,
                            this.format_block('jstpl_resource_icon', { type: resource }),
                            () => this.takeAction('actChooseResourceToStore', { resource: resource })
                        );
                        dojo.addClass(`buttonStore${resource}`, 'resourceButton');
                    });
                    this.addPrimaryActionButton(
                        'buttonActionPassStoring',
                        _('Pass'),
                        () => this.takeAction('actPassStoringResource')
                    );
                }
            }, 1);
        },
    });
});
