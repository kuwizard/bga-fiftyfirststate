define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseThreeAction', null, {
        constructor() {
            this._notifications.push(['resourcesSpentFaction', 1]);
            this._notifications.push(['locationRazed', 1]);
            this._notifications.push(['locationBuilt', 1]);
            this._notifications.push(['locationDealMade', 1]);
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
                dojo.query(`#hand .location`).forEach((location) => {
                    const id = this.extractId(location, 'location');
                    if (args.locations.includes(id)) {
                        this.addSelectableClass(location);
                        this.dojoConnect(location, () => {
                            this.takeAction('actUseLocation', { id: id });
                        })
                    } else {
                        dojo.addClass(location, 'unselectable');
                    }
                });
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

        onEnteringStateLocationActions(args) {
            if (this.isCurrentPlayerActive()) {
                dojo.query(`#hand .location`).forEach((location) => {
                    if (args.id === this.extractId(location, 'location')) {
                        this.addSelectedClass(location);
                    } else {
                        dojo.addClass(location, 'unselectable');
                    }
                });
                Object.keys(args.locationActionsLexemes).forEach((action) => {
                    if (args.actions.includes(action)) {
                        this.addPrimaryActionButton(
                            `button${action}`,
                            args.locationActionsLexemes[action],
                            () => this.takeAction(`actLocation${action.replace(/^./, action[0].toUpperCase())}`)
                        );
                    }
                });
                this.addUndoButton();
            }
        },

        addUndoButton() {
            this.addSecondaryActionButton(
                'buttonActionUndo',
                _('Undo'),
                () => this.takeAction('actUndo')
            );
        },

        notif_resourcesSpentFaction(n) {
            debug('Notif: resourcesSpentFaction', n);
            const element = this.querySingle(`#faction_${n.args.player_id} .spent[data-order="${n.args.order}"]`);
            n.args.resources.forEach((resource) => {
                this.placeResourceOnFactionAction(element, resource);
            })
        },

        notif_locationRazed(n) {
            debug('Notif: locationRazed', n);
            dojo.destroy(`location_${n.args.id}`);
        },

        notif_locationBuilt(n) {
            debug('Notif: locationBuilt', n);
            dojo.destroy(`location_${n.args.location.id}`);
            const rowElement = this.querySingle(`#faction_${n.args.player_id} .${n.args.factionRow}`);
            dojo.place(this.format_block('jstpl_location', n.args.location), rowElement);
        },

        notif_locationDealMade(n) {
            debug('Notif: locationDealMade', n);
            dojo.destroy(`location_${n.args.id}`);
            const dealsResourceBlock = this.querySingle(`#faction_${n.args.player_id} .${n.args.resource}Block`);
            if (dealsResourceBlock) {
                dojo.place(this.format_block('jstpl_resource_icon', { type: n.args.resource }), dealsResourceBlock);
            } else {
                this.addResourcesToDeals(n.args.player_id, { [n.args.resource]: 1 });
            }
        },
    });
});
