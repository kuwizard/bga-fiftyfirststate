define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseThreeAction', null, {
        constructor() {
            this._notifications.push(['locationBuilt', 1]);
            this._notifications.push(['locationDealMade', 1]);
            this._notifications.push(['resourcesPlacedOnLocation', 1]);
        },

        onEnteringStatePhaseThreeAction(args) {
            debug('Phase Three Action state', args);
            if (this.isCurrentPlayerActive()) {
                if (args.deploy.brick) {
                    this.addDeployButton('brick')
                }
                if (args.deploy.development) {
                    this.addDeployButton('devel')
                }
                if (args.spendWorkers) {
                    this.makeAreaSelectable('spendWorkersArea', 'actSpendWorkers');
                }
                if (args.factionActions) {
                    this.makeAreaSelectable('actionsArea', 'actEnableFactionActions');
                }
                this.makeLocationsSelectableAndClickable('#hand .location', 'actUseLocation', args.locations);
                this.makeLocationsSelectableAndClickable(
                    `#faction_${this.player_id} .location`,
                    'actActivateLocation',
                    args.locations
                );
                this.makeLocationsSelectableAndClickable(
                    `.factionBoard:not(#faction_${this.player_id}) .location`,
                    'actOpenProduction',
                    args.openProductions,
                );
                this.addPrimaryActionButton(
                    'buttonActionPass',
                    _('Pass'),
                    () => this.takeAction('actActionPass')
                );
            }
        },

        addDeployButton(postfix) {
            this.addPrimaryActionButton(
                `buttonDeploy${postfix}`,
                this.replaceWithResourceIcon((_('Deploy (spend {icon})')).replace('{icon}', `{${postfix}Icon}`)),
                () => this.takeAction('actDeploy', { resource: postfix })
            );
            dojo.addClass(`buttonDeploy${postfix}`, 'resourceButton');
        },

        makeLocationsSelectableAndClickable(locator, action, allowedList = null) {
            dojo.query(locator).forEach((location) => {
                const id = this.extractId(location, 'location');
                if (allowedList === null || allowedList.includes(id)) {
                    this.addSelectableClass(location);
                    this.dojoConnect(location, () => {
                        this.takeAction(action, { id: id });
                    })
                } else {
                    this.addUnselectableClass(location);
                }
            });
        },

        makeLocationsUnselectable(locator) {
            this.makeLocationsSelectableAndClickable(locator, '', []);
        },

        makeAreaSelectable(locator, action) {
            const area = this.querySingle(`#faction_${this.player_id} .${locator}`);
            this.addSelectableClass(area);
            this.dojoConnect(area, () => {
                this.takeAction(action);
            })
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
                            _(args.locationActionsLexemes[action]),
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

        notif_resourcesPlacedOnLocation(n) {
            debug('Notif: resourcesPlacedOnLocation', n);
            this.placeResourcesOnLocation(n.args.id, n.args.resources);
        },
    });
});
