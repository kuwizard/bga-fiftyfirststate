define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseFourCleanup', null, {
        constructor() {
            this._notifications.push(['playersResetAllResources', 600]);
            this._notifications.push(['playerGotResourcesFromStorage', 1]);
        },

        onEnteringStatePhaseFourCleanup(args) {
            debug('phaseFour Cleanup state', args);

        },

        notif_playersResetAllResources(n) {
            debug('Notif: playersResetAllResources', n);
            this.destroyAll('.spentArea .resourceIcon');
            this.destroyAll(`.actions .resourceIcon`);
            this.destroyAll(`.production .resourceIcon`);
            this.forEachPlayer((player) => {
                Object.keys(this.resourceCounters[player.id]).forEach(resource => {
                    if (resource !== 'card') this.resourceCounters[player.id][resource].toValue(0);
                });
                if (this.querySingle('#sticky') && n.args.player_id === this.player_id) {
                    Object.keys(this.resourceCounters.sticky).forEach(resource => {
                        if (resource !== 'card') this.resourceCounters.sticky[resource].toValue(0);
                    });
                }
            });

            dojo.query('.player-board .resourceIcon:not(.cardIcon)').forEach((resource) => {
                dojo.addClass(resource, 'blurred');
            });
        },

        async notif_playerGotResourcesFromStorage(n) {
            debug('Notif: playerGotResourcesFromStorage', n);
            for (const resource of Object.keys(n.args.resources)) {
                for (let i = 0; i < n.args.resources[resource]; i++) {
                    const resourceElement = this.querySingle(`#location_${n.args.location.id} .${resource}Icon`);
                    const playerBoardIcon = this.querySingle(`#overall_player_board_${n.args.player_id} .${resource}Icon`);
                    await this.slide(resourceElement, playerBoardIcon, { destroy: true });
                    this.querySingle(`#player_board_${n.args.player_id} .${resource}Value`).innerText = n.args.resources[resource];
                    dojo.removeClass(playerBoardIcon, 'blurred');
                    await new Promise(resolve => setTimeout(resolve, 200));
                }
            }
        },
    });
});
