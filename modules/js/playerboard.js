define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.playerboard', null, {
        constructor() {
            this._notifications.push(['resourcesChanged', 1]);
            this.tick = false;
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
                Object.keys(player.resources).forEach((resource) => {
                    if (player.resources[resource] === 0) {
                        dojo.addClass(
                            this.querySingle(`#overall_player_board_${player.id} .${resource}Icon`),
                            'blurred'
                        );
                    }
                });
                if (player.id === this.player_id) {
                    this.pinOrUnpinPlayerBoard(player.passed);
                    window.addEventListener(
                        "scroll",
                        () => {
                            if (!this.tick) {
                                setTimeout(() => {
                                    const isMobile = this.querySingle('.mobile_version');
                                    this.pinOrUnpinPlayerBoard(player.passed, isMobile);
                                    if (isMobile) {
                                        this.fixStickedBoardForMobile();
                                    }
                                    this.tick = false;
                                }, 50)
                            }
                            this.tick = true;
                        }
                    );
                }
            });
        },

        pinOrUnpinPlayerBoard(isPassed, isMobile) {
            const playerResources = this.querySingle(`#overall_player_board_${this.player_id} .playerResourcesWrapper`);
            if (playerResources.getBoundingClientRect().y < 0) {
                if (!this.querySingle('#sticky')) {
                    let newBoard = dojo.clone(playerResources);
                    dojo.attr(newBoard, 'id', 'sticky');
                    newBoard = dojo.place(newBoard, playerResources.parentNode);
                    const resourcesBoard = this.querySingle(`#overall_player_board_${this.player_id} .playerResourcesWrapper .playerResources`);
                    const width = isMobile ? resourcesBoard.getBoundingClientRect().width : playerResources.getBoundingClientRect().width;
                    dojo.style(newBoard, 'width', `${width}px`);
                    dojo.style(newBoard, 'height', `${playerResources.getBoundingClientRect().height}px`);
                    if (isPassed) {
                        dojo.addClass('sticky', 'passed');
                    }
                    dojo.removeClass(this.querySingle(`#overall_player_board_${this.player_id}`), 'passed');
                }
            } else {
                if (isPassed) {
                    dojo.addClass(this.querySingle(`#overall_player_board_${this.player_id}`), 'passed');
                }
                dojo.destroy('sticky');
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
                    this.querySingle(`#player_board_${n.args.player_id} .${resource}Value`).innerText = data[resource];
                    this.changeBlurState(
                        this.querySingle(`#player_board_${n.args.player_id} .${resource}Icon`),
                        data[resource]
                    );
                    const stickyResource = this.querySingle(`#sticky .${resource}Value`);
                    if (stickyResource && n.args.player_id === this.player_id) {
                        this.querySingle(`#sticky .${resource}Value`).innerText = data[resource];
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
        }
    });
});
