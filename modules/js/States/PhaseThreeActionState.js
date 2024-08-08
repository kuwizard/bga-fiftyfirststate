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
                    this.makeAreaSelectable('spendWorkersArea', 'actSpendWorkers');
                }
                if (args.factionActions) {
                    this.makeAreaSelectable('actionsArea', 'actEnableFactionActions');
                }
            }
        },

        makeAreaSelectable(locator, action) {
            const area = this.querySingle(`#faction_${this.player_id} .${locator}`);
            this.addSelectableClass(area);
            this.dojoConnect(area, () => {
                this.takeAction(action);
            })
        },

        onEnteringStateSpendWorkers() {
            if (this.isCurrentPlayerActive()) {
                ['fuel', 'gun', 'iron', 'brick', 'card'].forEach((resource) => {
                    this.addPrimaryActionButton(
                        `buttonGain${resource}`,
                        this.format_block('jstpl_resource_icon', { type: resource }),
                        () => this.takeAction('actGainResourceForWorkers', { resource: resource })
                    );
                });
                this.addSecondaryActionButton(
                    'buttonActionUndo',
                    _('Undo'),
                    () => this.takeAction('actUndo')
                );
            }
        },

        onEnteringStateFactionActions(args) {
            if (this.isCurrentPlayerActive()) {
                Object.keys(args).forEach((id) => {
                    const action = args[id];
                    const spendRequirements = action.spendRequirements.map((resource) => {
                        return this.format_block('jstpl_resource_icon', { type: resource });
                    });
                    const bonus = action.bonus.map((resource) => {
                        return this.format_block('jstpl_resource_icon', { type: resource });
                    });
                    const buttonId = `buttonGain${id}`;
                    this.addPrimaryActionButton(
                        buttonId,
                        `${spendRequirements.join('')} ➤ ${bonus.join('')}`,
                        () => this.takeAction('actFactionAct', { id: id })
                    );
                    dojo.addClass(buttonId, 'resourceButton');
                });
                this.addSecondaryActionButton(
                    'buttonActionUndo',
                    _('Undo'),
                    () => this.takeAction('actUndo')
                );
            }
        },
    });
});
