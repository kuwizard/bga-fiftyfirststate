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
                dojo.place(this.format_block('jstpl_player_board', player), 'player_board_' + player.id);
                if (player.id === this.player_id) {
                    this.pinOrUnpinPlayerBoard(player.passed);
                    window.addEventListener(
                        "scroll",
                        () => {
                            if (!this.tick) {
                                setTimeout(() => {
                                    this.pinOrUnpinPlayerBoard(player.passed);
                                    this.tick = false;
                                }, 50)
                            }
                            this.tick = true;
                        }
                    );
                }
            });
        },

        pinOrUnpinPlayerBoard(isPassed) {
            const playerResources = this.querySingle(`#overall_player_board_${this.player_id} .playerResourcesWrapper`);
            if (playerResources.getBoundingClientRect().y < 0) {
                if (!this.querySingle('#sticky')) {
                    let newBoard = dojo.clone(playerResources);
                    dojo.attr(newBoard, 'id', 'sticky');
                    newBoard = dojo.place(newBoard, playerResources.parentNode)
                    dojo.style(newBoard, 'width', `${playerResources.getBoundingClientRect().width}px`);
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

        async notif_resourcesChanged(n) {
            debug('Notif: resourcesChanged', n);
            await this.waitForDisappearance('.moving');
            const data = n.args.resources;
            Object.keys(data).forEach((resource) => {
                if (resource === 'score') {
                    this.scoreCtrl[n.args.player_id].toValue(data[resource]);
                } else {
                    this.querySingle(`#player_board_${n.args.player_id} .${resource}Value`).innerText = data[resource];
                    if (n.args.player_id === this.player_id) {
                        this.querySingle(`#sticky .${resource}Value`).innerText = data[resource];
                    }
                }
            });
        },
    });
});
