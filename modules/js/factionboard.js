define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.factionboard', null, {
        constructor() {
            this._notifications.push(['locationRuined', 1]);
        },

        addBoard() {
            dojo.place(this.format_block('jstpl_board', {}), 'game_play_area');
        },

        addFactionBoards() {
            this.forEachPlayer((player) => {
                const factionBoard = dojo.place(this.format_block('jstpl_faction_board', player), 'board');
                this.addResourcesToDeals(player.id, player.dealsResources);
                this.forEachFactionRow((row) => {
                    const rowElement = factionBoard.querySelector(`.${row}`);
                    player.locations[row].forEach((location) => {
                        this.addLocation(location, rowElement);
                        if (location.resources) {
                            this.placeResourcesOnLocation(location.id, location.resources);
                        }
                    });
                });

                Object.keys(player.usedFactionActions).forEach((order) => {
                    const spentArea = this.querySingle(`#faction_${player.id} .spent[data-order="${order}"]`);
                    player.usedFactionActions[order].forEach((resource) => {
                        this.placeResourceOnFactionAction(spentArea, resource);
                    })
                })
            });
        },

        addResourcesToDeals(playerId, resources) {
            Object.keys(resources).forEach((resource) => {
                const dealsLocation = this.querySingle(`#faction_${playerId} .deals`);
                const block = dojo.place(
                    this.format_block('jstpl_resource_block', { type: resource }),
                    dealsLocation
                );
                for (let i = 0; i < resources[resource]; i++) {
                    dojo.place(this.format_block('jstpl_resource_icon', { type: resource }), block);
                }
            });
        },

        placeResourceOnFactionAction(element, resource) {
            const res = dojo.place(this.format_block('jstpl_resource_icon', { type: resource }), element);
            this.addSomeRandomMargins(res);
        },

        placeResourcesOnLocation(id, resources) {
            resources.forEach((resource) => {
                const resourceElement = dojo.place(
                    this.format_block('jstpl_resource_icon', { type: resource }),
                    this.querySingle(`#location_${id} .resources`)
                );
                this.addSomeRandomMargins(resourceElement);
            });
        },

        notif_locationRuined(n) {
            debug('Notif: locationRuined', n);
            dojo.addClass(`location_${n.args.location.id}`, 'ruined');
            this.destroyAll(`#location_${n.args.location.id} .resourceIcon`);
        },
    });
});
