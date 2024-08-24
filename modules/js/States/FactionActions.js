define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.factionActions', null, {
        constructor() {
            this._notifications.push(['resourcesSpentFaction', 1]);
        },

        onEnteringStateSpendWorkers() {
            if (this.isCurrentPlayerActive()) {
                ['fuel', 'gun', 'iron', 'brick', 'card'].forEach((resource) => {
                    this.addPrimaryActionButton(
                        `buttonGain${resource}`,
                        this.format_block('jstpl_resource_icon', { type: resource }),
                        () => this.takeAction('actGainResourceForWorkers', { resource: resource })
                    );
                    dojo.addClass(`buttonGain${resource}`, 'resourceButton');
                });
                this.addUndoButton();
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
                this.addUndoButton();
            }
        },

        onEnteringStateDiscardLocationForResources() {
            if (this.isCurrentPlayerActive()) {
                this.makeAllSelectableAndClickable(this.getHand(), this.discardLocation.bind(this));
            }
        },

        discardLocation(location) {
            this.takeAction('actDiscardLocation', { id: this.extractId(location, 'location') });
        },

        notif_resourcesSpentFaction(n) {
            debug('Notif: resourcesSpentFaction', n);
            const element = this.querySingle(`#faction_${n.args.player_id} .spent[data-order="${n.args.order}"]`);
            n.args.resources.forEach((resource) => {
                this.placeResourceOnFactionAction(element, resource);
            })
        },
    });
});
