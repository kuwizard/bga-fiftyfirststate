define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseThreeAction', null, {
        constructor() {
            this._notifications.push(['locationBuilt', 1]);
            this._notifications.push(['locationDealMade', 1]);
            this._notifications.push(['resourcesPlacedOnLocation', 1]);
            this._notifications.push(['connectionTaken', 1]);
            this._notifications.push(['connectionPlayed', 1]);
            this._notifications.push(['playerPassed', 1]);
        },

        async onEnteringStatePhaseThreeAction(args) {
            debug('Phase Three Action state', args);
            if (this.isCurrentPlayerActive()) {
                await this.waitForDisappearance('.moving');
                if (args.spendWorkers) {
                    this.makeAreaSelectable('spendWorkersArea', 'actSpendWorkers');
                }
                if (args.factionActions) {
                    this.makeAreaSelectable('actionsArea', 'actEnableFactionActions', { combined: false });
                }
                this.makeConnectionsSelectableAndClickable(args.connectionsToTake, false, 'actTakeConnection');
                this.makeConnectionsSelectableAndClickable(args.connectionsToPlay, true, 'actPlayConnection');
                this.makeLocationsSelectableAndClickable('#handLocations .location', 'actUseLocation', args.locations);
                this.makeLocationsSelectableAndClickable(
                    `#faction_${this.player_id} .location`,
                    'actActivateLocation',
                    args.locations
                );
                this.makeLocationsSelectableAndClickable(
                    `.factionBoard:not(#faction_${this.player_id}) .location`,
                    'actUseOtherPlayerLocation',
                    args.otherPlayersLocations,
                );
                this.makeLocationsUnselectable('#discard .location');
                if (this.noActionsAvailable(args)) {
                    this.gamedatas.gamestate.descriptionmyturn = _('No possible actions left');
                    this.updatePageTitle();
                }
                this.addAllActionButtons(args);
                this.addDangerActionButton(
                    'buttonActionPass',
                    _('Pass'),
                    this.wrapIntoConfirmation(
                        this.getPassWarningLexeme(),
                        () => this.takeAction('actActionPass')
                    ),
                );
                Object.keys(args.otherPlayersResources).forEach((pId) => {
                    this.makeResourcesSelectableAndClickable(pId, args.otherPlayersResources[pId]);
                })
            }
        },

        noActionsAvailable(args) {
            return args.connectionsToPlay.length === 0
                && args.connectionsToTake.length === 0
                && !args.develop.ammo && !args.develop.brick && !args.develop.development
                && !args.factionActions
                && Object.keys(args.locations).length === 0
                && args.otherPlayersLocations.length === 0
                && !args.spendWorkers;
        },

        addDevelopButton(postfix) {
            this.addPrimaryActionButton(
                `buttonDevelop${postfix}`,
                this.replaceWithResourceIcon((_('Develop (spend {icon})')).replace('{icon}', `{${postfix}Icon}`)),
                () => this.takeAction('actDevelop', { resource: postfix })
            );
            dojo.addClass(`buttonDevelop${postfix}`, 'resourceButton');
        },

        makeLocationsSelectableAndClickable(locator, action, allowedList = null) {
            dojo.query(locator).forEach((location) => {
                const id = this.extractId(location, 'location');
                const allowedIds = allowedList && Object.keys(allowedList).map((id) => parseInt(id, 10));
                if (allowedList === null || allowedIds.includes(id)) {
                    this.addSelectableClass(location);
                    this.dojoConnect(location, () => {
                        this.wrapIntoCardConfirmation(() => this.takeAction(action, { id: id }), allowedList[id])()
                    })
                } else {
                    this.addUnselectableClass(location);
                }
            });
        },

        makeConnectionsSelectableAndClickable(allowedList, isHand, action) {
            const locator = isHand ? '#handConnections' : '#deckConnectionsBlock';
            dojo.query(`${locator} .connection:not(.back)`).forEach((connection) => {
                const id = this.extractId(connection, 'connection');
                if (allowedList.includes(id)) {
                    this.addSelectableClass(connection);
                    this.dojoConnect(connection, () => {
                        this.takeAction(action, { id: id });
                    })
                } else {
                    this.addUnselectableClass(connection);
                }
            });
        },

        makeLocationsUnselectable(locator) {
            this.makeLocationsSelectableAndClickable(locator, '', []);
        },

        makeAreaSelectable(locator, action, opts = {}) {
            const area = this.querySingle(`#faction_${this.player_id} .${locator}`);
            this.addSelectableClass(area);
            this.dojoConnect(area, () => {
                this.takeAction(action, opts);
            })
        },

        makeResourcesSelectableAndClickable(pId, resources) {
            resources.forEach((resourceName) => {
                const resourceElement = this.querySingle(`#player_board_${pId} .${resourceName}`);
                this.addSelectableClass(resourceElement);
                this.dojoConnect(resourceElement, () => {
                    this.wrapIntoCardConfirmation(
                        () => this.takeAction('actUseOpenProduction', { resourceName: resourceName, pId: pId }),
                        resourceName === 'card'
                    )()
                })
            });
        },

        onEnteringStateLocationActions(args) {
            if (this.isCurrentPlayerActive()) {
                dojo.query(`#handLocations .location`).forEach((location) => {
                    if (args.id === this.extractId(location, 'location')) {
                        this.addSelectedClass(location);
                    } else {
                        dojo.addClass(location, 'unselectable');
                    }
                });
                Object.keys(args.locationActionsLexemes).forEach((actionName) => {
                    if (Object.keys(args.actions).includes(actionName)) {
                        this.addPrimaryActionButton(
                            `button${actionName}`,
                            _(args.locationActionsLexemes[actionName]),
                            this.wrapIntoCardConfirmation(
                                () => this.takeAction(`actLocation${actionName.replace(
                                    /^./,
                                    actionName[0].toUpperCase()
                                )}`),
                                args.actions[actionName],
                            )
                        );
                    }
                });
                this.addUndoButton();
            }
        },

        onEnteringStateOpenProductionOrRaze(args) {
            if (this.isCurrentPlayerActive()) {
                this.addSelectedClass(this.querySingle(`#location_${args.locationId}`));
                this.addPrimaryActionButton('buttonOpenProd', _('Use it as open production'),
                    this.wrapIntoCardConfirmation(() => this.takeAction('actOptionOpenProduction', {}), args.openProd)
                );
                this.addPrimaryActionButton('buttonRazeIt', _('Raze it'),
                    this.wrapIntoCardConfirmation(() => this.takeAction('actOptionRaze', {}), args.raze)
                );
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

        wrapIntoCardConfirmation(callback, condition) {
            return this.wrapIntoConfirmation(this.getCardWarningLexeme(), callback, condition);
        },

        notif_locationBuilt(n) {
            debug('Notif: locationBuilt', n);
            const rowElement = this.querySingle(`#faction_${n.args.player_id} .${n.args.factionRow}`);
            let location = this.querySingle(`#location_${n.args.location.id}`);
            if (location) {
                dojo.removeClass(location, 'selected');
            } else {
                location = this.addLocation(n.args.location, $(`overall_player_board_${n.args.player_id}`), true);
            }
            this.slide(location, rowElement, { phantomEnd: true });
        },

        async notif_locationDealMade(n) {
            debug('Notif: locationDealMade', n);
            const deals = this.querySingle(`#faction_${n.args.player_id} .deals`);
            if (this.player_id === n.args.player_id) {
                const location = this.querySingle(`#location_${n.args.location.id}`);
                this.addClass(location, 'turnAround');
                this.addClass(location, 'turning', true, 450);
                await this.waitForDisappearance('.turning');
                await this.slide(location, deals, { destroy: true, pos: { x: 50, y: 0 }, duration: 700 });
            }
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

        notif_connectionTaken(n) {
            debug('Notif: connectionTaken', n);
            const connectionCard = this.querySingle(`#connection_${n.args.id}`)
            const clone = dojo.clone(connectionCard);
            const firstConnectionId = this.extractId(
                this.querySingle('#deckConnectionsBlock .connection'),
                'connection'
            );
            const position = firstConnectionId === n.args.id ? 'first' : 'last';
            dojo.attr(clone, 'id', 'connection_0');
            dojo.addClass(clone, 'back');
            const parent = connectionCard.parentNode;
            if (n.args.player_id === this.player_id) {
                this.slide(connectionCard, 'handConnections');
            } else {
                this.slide(connectionCard, `overall_player_board_${n.args.player_id}`, { destroy: true });
            }
            dojo.place(clone, parent, position);
            dojo.removeClass(clone, 'selectable');
        },

        notif_connectionPlayed(n) {
            debug('Notif: connectionPlayed', n);
            this.fadeOutAndDestroyAll(`#connection_${n.args.id}`);
        },

        notif_playerPassed(n) {
            debug('Notif: playerPassed', n);
            this.setPlayerPass(n.args.player_id)
        },
    });
});
