define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseThreeAction', null, {
        constructor() {
        },

        onEnteringStatePhaseThreeAction(args) {
            debug('Phase Three Action state', args);
            if (this.isCurrentPlayerActive()) {
                this.addPrimaryActionButton(
                    'buttonActionPass',
                    _('Pass'),
                    () => this.takeAction('actActionPass')
                );
                if (args.spendWorkers) {
                    const spendWorkers = this.querySingle(`#faction_${this.player_id} .spendWorkersArea`);
                    this.addSelectableClass(spendWorkers);
                    this.dojoConnect(spendWorkers, () => {
                        this.takeAction('actSpendWorkers')
                    })
                }
            }
        },

        onEnteringStateSpendWorkers() {
            if (this.isCurrentPlayerActive()) {
                ['fuel', 'gun', 'iron', 'brick', 'card'].forEach((resource) => {
                    this.addPrimaryActionButton(
                        `buttonGain${resource}`,
                        this.format_block('jstpl_resource_icon', { type: resource }),
                        () => this.takeAction('actGainResource', { resource: resource })
                    );
                });
                this.addSecondaryActionButton(
                    'buttonActionUndo',
                    _('Undo'),
                    () => this.takeAction('actUndoSpend')
                );
            }
        },
    });
});
