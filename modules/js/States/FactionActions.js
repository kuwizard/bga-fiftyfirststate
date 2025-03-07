define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.factionActions', null, {
        constructor() {
            this._notifications.push(['resourcesSpentFaction', 1]);
        },

        onEnteringStateSpendWorkers() {
            if (this.isCurrentPlayerActive()) {
                ['fuel', 'gun', 'iron', 'brick', 'card'].forEach((resource) => {
                    this.addActionButtonWithResource(resource, 'actGainResourceForWorkers');
                });
                this.makeLocationsUnselectable('.location');
                this.makeLocationsUnselectable('.connection');
                this.addUndoButton();
            }
        },

        onEnteringStateFactionActions(args) {
            if (this.isCurrentPlayerActive()) {
                this.showIconButtonsFromArgs(args);
                this.makeLocationsUnselectable('.location');
                this.makeLocationsUnselectable('.connection');
                this.addUndoButton();
            }
        },

        onEnteringStateDiscardLocationForResources() {
            if (this.isCurrentPlayerActive()) {
                this.makeAllSelectableAndClickable(this.getHand(), this.discardLocation.bind(this));
                this.makeAllSelectableAndClickable(
                    dojo.query('#handConnections .connection'),
                    this.discardConnection.bind(this)
                );
            }
        },

        showIconButtonsFromArgs(args) {
            Object.keys(args).forEach((id) => {
                const action = args[id];
                const spendRequirements = action.spendRequirements.map((resource) => {
                    return this.format_block('jstpl_resource_icon', { type: resource });
                });
                let multiBonus = false;
                const bonus = action.bonus.map((resource) => {
                    if (resource === 'multi') {
                        const multi = ['iron', 'gun', 'fuel', 'brick', 'card'].map((resource) => {
                            return this.format_block('jstpl_resource_icon', { type: resource });
                        });
                        multiBonus = true;
                        return multi.join('/');
                    } else {
                        return this.format_block('jstpl_resource_icon', { type: resource });
                    }
                });
                const buttonId = `buttonGain${id}`;
                const callback = multiBonus ?
                    () => this.takeAction('actSpendWorkers', {}) :
                    () => this.takeAction('actFactionAct', { id: id });
                this.addPrimaryActionButton(
                    buttonId,
                    `${spendRequirements.join('')} ➤ ${bonus.join('')}`,
                    callback
                );
                dojo.addClass(buttonId, 'resourceButton');
            });
        },

        addAllActionButtons(args) {
            const buttons = [
                {
                    condition: args.factionActions || args.spendWorkers,
                    name: 'factionActions',
                    label: _('Faction Action(s)'),
                    callback: () => this.takeAction('actEnableFactionActions', { combined: true })
                },
            ];
            buttons.forEach((buttonObject) => {
                if (buttonObject.condition) {
                    this.addPrimaryActionButton(
                        `button${buttonObject.name}`,
                        buttonObject.label,
                        buttonObject.callback,
                    );
                }
            });
            if (args.develop.brick) {
                this.addDevelopButton('brick')
            }
            if (args.develop.development) {
                this.addDevelopButton('devel')
            }
            if (args.develop.ammo) {
                this.addDevelopButton('ammo')
            }
            if (args.placeDefence) {
                this.addPlaceDefenceButton();
            }
        },

        discardLocation(location) {
            this.takeAction('actDiscardLocation', { id: this.extractId(location, 'location') });
        },

        addDevelopButton(postfix) {
            this.addPrimaryActionButton(
                `buttonDevelop${postfix}`,
                this.replaceWithResourceIcon((_('Develop (spend {icon})')).replace('{icon}', `{${postfix}Icon}`)),
                () => this.takeAction('actDevelop', { resource: postfix })
            );
            dojo.addClass(`buttonDevelop${postfix}`, 'resourceButton');
        },

        addPlaceDefenceButton() {
            this.addPrimaryActionButton(
                `buttonPlaceDefence`,
                this.replaceWithResourceIcon(_('Place {defenceIcon}')),
                () => this.takeAction('actEnablePlaceDefenceState')
            );
            dojo.addClass(`buttonPlaceDefence`, 'resourceButton');
        },

        discardConnection(location) {
            this.takeAction('actDiscardConnection', { id: this.extractId(location, 'connection') });
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
