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
            dojo.query('.player-board .resourceValue:not(.cardValue)').forEach((resource) => {
                resource.innerText = 0;
            });
        },

        async notif_playerGotResourcesFromStorage(n) {
            debug('Notif: playerGotResourcesFromStorage', n);
            for (const resource of Object.keys(n.args.resources)) {
                for (let i = 0; i < n.args.resources[resource]; i++) {
                    const resourceElement = this.querySingle(`#location_${n.args.locationId} .${resource}Icon`);
                    const playerBoardIcon = this.querySingle(`#overall_player_board_${n.args.player_id} .${resource}Icon`);
                    this.slide(resourceElement, playerBoardIcon, { destroy: true }).then(() => {
                        this.querySingle(`#player_board_${n.args.player_id} .${resource}Value`).innerText = n.args.resources[resource];
                    });
                    await new Promise(resolve => setTimeout(resolve, 200));
                }
            }
        },
    });
});
