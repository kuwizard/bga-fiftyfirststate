define(['dojo', 'dojo/_base/declare', 'dijit/Tooltip'], (dojo, declare) => {
    return declare('state.chooseFaction', null, {
        constructor() {
            this._notifications.push(['gameStateMultipleActiveUpdate', 1]);
            this._notifications.push(['applyFactions', 1]);
        },

        onEnteringStateChooseFaction(args) {
            debug('ChooseFaction state', args);
            dojo.query('.factionBoard, #deckConnectionsBlock, #hand').forEach((element) => {
                dojo.addClass(element, 'hidden');
            });
            if (!this.isSpectator) {
                dojo.place(this.format_block('jstpl_faction_chooser', this.getHeaderLexemes()), 'board');
                this.forEachPlayer((player) => {
                    dojo.style(this.querySingle(`#player_name_${player.id} a`), 'color', '#000');
                    this.gamedatas.players[player.id].color = '000';
                });
                args.prodActions.forEach((faction, index) => {
                    const actions = this.replaceWithResourceIcon(this.getFactionActionLexeme(index).join(
                        '<br/>'), true);
                    dojo.place(
                        this.format_block(
                            'jstpl_faction_to_choose',
                            {
                                type: index,
                                altType: index + 4,
                                productionHeader: this.getProductionHeaderLexeme(),
                                production: this.getProduction(faction),
                                actionsHeader: this.getActionsHeaderLexeme(),
                                priorityHeader: this.getPriorityHeaderLexeme(),
                                actions: actions, ...this.getSliderLexemes()
                            }
                        ),
                        'factions'
                    );
                });
                Object.keys(args._private).forEach((prefId) => {
                    const intValue = parseInt(prefId, 10);
                    if (intValue < 400) {
                        if (intValue % 200 < 100) {
                            const preferenceValue = args._private[prefId] === '-1' ? '0' : args._private[prefId];
                            this.querySingle(`#priorityDropdown_${intValue - 201}`).value = preferenceValue;
                        } else {
                            this.querySingle(`#switch_${intValue - 301}`).checked = args._private[prefId] !== '1';
                        }
                    }
                });
                if (this.gamedatas.gamestate.name === 'chooseFaction') {
                    if (this.isCurrentPlayerActive()) {
                        this.connectHandlers();
                        this.addDoneButtonIfNeeded();
                        this.addIDontCareButton();
                    } else {
                        this.addChangedMindButton();
                        this.changeDropdownsAndCheckboxesStates(false);
                    }
                }
            }
        },

        collectDropdownsAndSetRemaining(id, newValue) {
            const dropdownsElements = dojo.query('#factions .priorityDropdown');

            const valuesMap = this.getSortedDropdowns(dropdownsElements).reduce((
                cnt,
                cur
            ) => (cnt[cur] = cnt[cur] + 1 || 1, cnt), {});
            delete valuesMap[0];
            // if this value was set anywhere else - change
            if (Object.values(valuesMap).includes(2)) {
                const outDatedDropdown = dropdownsElements.find((dropdown) => {
                    return dropdown.value === newValue && dropdown.id !== id;
                });
                outDatedDropdown.value = ['1', '2', '3', '4'].find((value) => {
                    return !Object.keys(valuesMap).includes(value);
                });
            }

            // Set last priority
            if (JSON.stringify(this.getSortedDropdowns(dropdownsElements)) === JSON.stringify([0, 1, 2, 3])) {
                dropdownsElements.forEach((dropdown) => {
                    if (dropdown.value === '0') {
                        dropdown.value = '4';
                    }
                });
            }
        },

        getSortedDropdowns(dropdownsElements) {
            const dropdowns = dropdownsElements.map((dropdown) => {
                return parseInt(dropdown.value);
            });
            return dropdowns.sort(function (a, b) {
                return a - b;
            });
        },

        addDoneButtonIfNeeded() {
            const needed = !this.getAllDropDownValues().includes('0');
            if (needed) {
                this.addPrimaryActionButton(
                    'buttonConfirmPreferences',
                    _('Save preferences'),
                    () => {
                        // If we send them altogether - mysql_deadlock may appear. We want to pause between setting preferences
                        [201, 202, 203, 204].forEach((bgaPrefId, index) => {
                            const values = this.getAllDropDownValues(true);
                            setTimeout(() => {
                                this.setGameUserPreference(bgaPrefId, values[index]);
                            }, 100 + bgaPrefId * 10);
                        });
                        [301, 302, 303, 304].forEach((bgaPrefId, index) => {
                            const value = this.querySingle(`#switch_${index}`).checked ? '2' : '1';
                            setTimeout(() => {
                                this.setGameUserPreference(
                                    bgaPrefId,
                                    value
                                );
                            }, 500 + bgaPrefId * 10);
                        });
                        this.takeAction(
                            'actChooseFactionsPreferences',
                            {
                                factions: this.getAllDropDownValues().join(';'),
                                sides: this.getAllSidesValues().join(';')
                            }
                        );
                    }
                );
            }
        },

        addIDontCareButton() {
            this.addDangerActionButton(
                'buttonIDontCare',
                _('I don\'t care'),
                this.wrapIntoConfirmation(
                    this.getIDontCareFactionChooseLexeme(),
                    () => {
                        [201, 202, 203, 204].forEach((bgaPrefId) => {
                            setTimeout(() => {
                                this.setGameUserPreference(bgaPrefId, 0);
                            }, 100 + bgaPrefId * 10);
                        });
                        [301, 302, 303, 304].forEach((bgaPrefId) => {
                            setTimeout(() => {
                                this.setGameUserPreference(bgaPrefId, 0);
                            }, 500 + bgaPrefId * 10);
                        });
                        dojo.query('.priorityDropdown').forEach((dropdown) => {
                            dropdown.value = '0';
                        });
                        this.takeAction('actIDontCare');
                    }
                ),
            );
        },

        addChangedMindButton() {
            this.addSecondaryActionButton('buttonChange', _('I changed my mind!'),
                () => this.takeAction('actChangedMind', {}, false)
            );
        },

        connectHandlers() {
            [0, 1, 2, 3].forEach((index) => {
                dojo.connect($(`factionInfo_${index}`), 'click', () => {
                    this.openTooltip(index);
                });
                dojo.connect($(`factionInfo_${index + 4}`), 'click', () => {
                    this.openTooltip(index + 4);
                });
                this.querySingle(`#priorityDropdown_${index}`).addEventListener('change', (e) => {
                    if (e.target.value !== 0) {
                        this.collectDropdownsAndSetRemaining(`priorityDropdown_${index}`, e.target.value);
                        this.clearPossible();
                        this.addDoneButtonIfNeeded();
                        this.addIDontCareButton();
                    }
                });
            });
        },

        getAllDropDownValues(intFormat = false) {
            const values = dojo.query('#factions .priorityDropdown').map(dropdown => dropdown.value);
            return intFormat ? values.map(value => parseInt(value, 10)) : values;
        },

        getAllSidesValues() {
            return dojo.query('.switch').map(dropdown => dropdown.checked ? '2' : '1');
        },

        openTooltip(factionType) {
            const factionBoard = this.format_block('jstpl_faction_board', { id: 0, faction: factionType });
            const label = this.format_block('jstpl_midsize_dialog', { content: factionBoard });
            const some = new dijit.Tooltip({
                position: this.defaultTooltipPosition,
                showDelay: 500,
                label: label,
                connectId: [`factionInfo_${factionType}`]
            });
            const element = $(`factionInfo_${factionType}`);
            some.open(element);
            dojo.connect(element, "mouseleave", (function () {
                some.close()
            }));
        },

        getHeaderLexemes() {
            return {
                header: this.getFactionChooserHeaderLexeme(),
                disclaimer: this.getFactionChooserDisclaimerLexeme(),
            };
        },

        getSliderLexemes() {
            return {
                optionOne: this.getFactionChooserLeftSideLexeme(),
                optionTwo: this.getFactionChooserRightSideLexeme(),
            };
        },

        getProduction(faction) {
            const productionArray = faction.order.map((resource) => {
                return `${faction.production[resource]} ${this.format_block(
                    'jstpl_resource_icon',
                    { type: resource }
                )}`
            });
            return `${productionArray[0]} ${productionArray[1]}<br/>${productionArray[2]} ${productionArray[3]}`;
        },

        changeDropdownsAndCheckboxesStates(enable) {
            dojo.query('#factions .priorityDropdown').forEach((dropdown) => {
                dropdown.disabled = !enable;
            });
            dojo.query('.switch').forEach((checkbox) => {
                checkbox.disabled = !enable;
            });
        },

        notif_gameStateMultipleActiveUpdate(n) {
            debug('Notif: gameStateMultipleActiveUpdate', n);
            if (this.gamedatas.gamestate.name === 'chooseFaction') {
                if (n.args.includes("" + this.player_id)) {
                    this.changeDropdownsAndCheckboxesStates(true);
                    this.connectHandlers();
                    this.addDoneButtonIfNeeded();
                    this.addIDontCareButton();
                } else {
                    this.changeDropdownsAndCheckboxesStates(false);
                    this.addChangedMindButton();
                }
            }
        },

        notif_applyFactions(n) {
            debug('Notif: applyFactions', n);
            dojo.destroy('factionChooser');
            dojo.query('.factionBoard, #deckConnectionsBlock, #hand').forEach((element) => {
                dojo.removeClass(element, 'hidden');
            });
            this.forEachPlayer((player) => {
                const data = n.args[player.id];
                dojo.style(this.querySingle(`#player_name_${player.id} a`), 'color', `#${data.color}`);
                dojo.style(this.querySingle(`#faction_${player.id} .playername`), 'color', `#${data.color}`);
                const factionWithOffset = this.getFactionWithOffset(data.faction, data.side);
                dojo.attr(this.querySingle(`#faction_${player.id} .faction`), 'data-faction', factionWithOffset);
                this.gamedatas.players[player.id].color = data.color;
                this.addFactionTooltips(player.id, data.faction);
            });
        },
    });
});
