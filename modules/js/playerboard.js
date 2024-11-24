define(['dojo', 'dojo/_base/declare', 'ebg/counter'], (dojo, declare) => {
    return declare('state.playerboard', null, {
        constructor() {
            this._notifications.push(['resourcesChanged', 1]);
            this.tick = false;
            this.resourceCounters = {};
        },

        markPassed(players) {
            Object.values(players).forEach((player) => {
                if (player.passed) {
                    dojo.addClass(`overall_player_board_${player.id}`, 'passed');
                }
            })
        },

        addResourcesTable() {
            this.forEachPlayer((player) => {
                dojo.place(this.format_block('jstpl_player_board', player.resources), 'player_board_' + player.id);
                this.resourceCounters[player.id] = {};
                Object.keys(player.resources).forEach((resource) => {
                    this.resourceCounters[player.id][resource] = new ebg.counter();
                    this.resourceCounters[player.id][resource].create(this.querySingle(`#overall_player_board_${player.id} .${resource}Value`));
                    this.resourceCounters[player.id][resource].setValue(player.resources[resource]);
                    if (player.resources[resource] === 0) {
                        dojo.addClass(
                            this.querySingle(`#overall_player_board_${player.id} .${resource}Icon`),
                            'blurred'
                        );
                    }
                });
                if (player.id === this.player_id) {
                    this.pinOrUnpinPlayerBoard();
                    window.addEventListener(
                        "scroll",
                        () => {
                            if (!this.tick) {
                                setTimeout(() => {
                                    this.pinOrUnpinPlayerBoard();
                                    this.tick = false;
                                }, 50)
                            }
                            this.tick = true;
                        }
                    );
                }
                if (player.isFirst) {
                    dojo.place(
                        this.format_block('jstpl_first_player', {}),
                        this.querySingle(`#player_board_${player.id} .firstPlayerWrapper`)
                    );
                }
                if (Object.keys(this.gamedatas.players).length === 4) {
                    const element = this.querySingle(`#overall_player_board_${player.id} .playerResourcesWrapper`);
                    dojo.addClass(element, 'fourplayers');
                }
            });
        },

        pinOrUnpinPlayerBoard(isMobile) {
            const playerResources = this.querySingle(`#overall_player_board_${this.player_id} .playerResourcesWrapper .playerResources`);
            const isPassed = this.gamedatas.players[this.player_id].passed;
            if (playerResources.getBoundingClientRect().y < 0) {
                if (!this.querySingle('#sticky')) {
                    let newBoard = dojo.clone(playerResources);
                    dojo.attr(newBoard, 'id', 'sticky');
                    newBoard = dojo.place(newBoard, playerResources.parentNode);
                    const resourcesBoard = this.querySingle(`#overall_player_board_${this.player_id} .playerResourcesWrapper .playerResources`);
                    const width = isMobile ? resourcesBoard.getBoundingClientRect().width : playerResources.getBoundingClientRect().width;
                    dojo.style(newBoard, 'width', `${width}px`);
                    if (isPassed) {
                        dojo.addClass('sticky', 'passed');
                    }
                    dojo.removeClass(this.querySingle(`#overall_player_board_${this.player_id}`), 'passed');
                    this.resourceCounters.sticky = {};
                    Object.keys(this.resourceCounters[this.player_id]).forEach((resource) => {
                        if (resource !== 'card') {
                            this.resourceCounters.sticky[resource] = new ebg.counter();
                            this.resourceCounters.sticky[resource].create(this.querySingle(`#sticky .${resource}Value`));
                            this.resourceCounters.sticky[resource].setValue(this.resourceCounters[this.player_id][resource].getValue());
                        }
                    });
                }
            } else {
                if (this.resourceCounters.sticky) {
                    if (isPassed) {
                        dojo.addClass(this.querySingle(`#overall_player_board_${this.player_id}`), 'passed');
                    }
                    // Sometimes ebg counters are visually not updated if they were not on screen, updating them again
                    Object.keys(this.resourceCounters.sticky).forEach((resource) => {
                        this.resourceCounters[this.player_id][resource].setValue(this.resourceCounters.sticky[resource].getValue());
                    });
                    dojo.destroy('sticky');
                    delete this.resourceCounters.sticky;
                }
            }
            if (this.querySingle('.mobile_version')) {
                this.fixStickedBoardForMobile();
            }
        },

        fixStickedBoardForMobile() {
            const stickedBoard = this.querySingle('#sticky');
            if (stickedBoard) {
                if (this.querySingle('.fixed-page-title')) {
                    dojo.addClass(stickedBoard, 'statusBar');
                } else {
                    dojo.removeClass(stickedBoard, 'statusBar');
                }
            }
        },

        async notif_resourcesChanged(n) {
            debug('Notif: resourcesChanged', n);
            await this.waitForDisappearance('.moving');
            const data = n.args.resources;
            Object.keys(data).forEach((resource) => {
                if (resource === 'score') {
                    this.scoreCtrl[n.args.player_id].toValue(data[resource]);
                } else {
                    this.resourceCounters[n.args.player_id][resource].toValue(data[resource]);
                    this.changeBlurState(
                        this.querySingle(`#player_board_${n.args.player_id} .${resource}Icon`),
                        data[resource]
                    );
                    if (n.args.player_id === this.player_id && this.querySingle(`#sticky .${resource}Value`)) {
                        this.resourceCounters.sticky[resource].toValue(data[resource]);
                        this.changeBlurState(
                            this.querySingle(`#sticky .${resource}Icon`),
                            data[resource]
                        );
                    }
                }
            });
        },

        changeBlurState(element, newAmount) {
            if (newAmount === 0) {
                dojo.addClass(element, 'blurred');
            } else {
                dojo.removeClass(element, 'blurred');
            }
        },
    });
});
