define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    const PLAYER_OPTION_SCROLLABLE = 402;
    const SCROLLABLE_DISABLE = 0;

    return declare('state.factionboard', null, {
        constructor() {
            this._notifications.push(['locationRuined', 1]);
        },

        addFactionBoards() {
            this.forEachPlayer((player) => {
                const boardData = { ...player, faction: this.getFactionWithOffset(player.faction, player.factionSide) };
                const factionBoard = dojo.place(this.format_block('jstpl_faction_board', boardData), 'board');
                dojo.place(this.coloredPlayerName(player.id), this.querySingle(`#faction_${player.id} .name`));
                this.addResourcesToDeals(player.id, player.dealsResources);
                this.forEachFactionRow((row) => {
                    const rowElement = factionBoard.querySelector(`.${row}`);
                    player.locations[row].forEach((location) => {
                        this.addLocation(location, rowElement);
                        if (location.isDefended) {
                            this.placeResourcesOnLocation(location.id, ['defence']);
                        }
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
                });
                this.addFactionTooltips(player.id, player.faction);
                if (this.getGameUserPreference(PLAYER_OPTION_SCROLLABLE) === SCROLLABLE_DISABLE) {
                    dojo.removeClass(factionBoard, 'scrollable');
                }
            });
        },

        getFactionWithOffset(faction, side) {
            const factionSpriteOffset = side === 1 ? 4 : 0;
            return faction + factionSpriteOffset;
        },

        addFactionTooltips(playerId, faction) {
            const featureTooltip = this.replaceWithResourceIcon(this.getFeatureAreaLexeme(), true);
            this.addTooltipHtml(`featureArea_${playerId}`, featureTooltip);
            const workersTooltip = this.replaceWithResourceIcon(this.getWorkersActionLexeme(), true);
            this.addTooltipHtml(`spendWorkersArea_${playerId}`, workersTooltip);
            const factionActions = this.replaceWithResourceIcon(this.getFactionActionLexeme(faction).join(
                '<br/>'), true);
            this.addTooltipHtml(`actionsArea_${playerId}`, factionActions);
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

        async placeResourcesOnLocation(id, resources) {
            await this.waitForDisappearance('.moving');
            resources.forEach((resource) => {
                const resourceElement = dojo.place(
                    this.format_block('jstpl_resource_icon', { type: resource }),
                    this.querySingle(`#location_${id} .resources`)
                );
                this.addSomeRandomMargins(resourceElement);
            });
        },

        onGameUserPreferenceChanged: function (prefId, prefValue) {
            let callback;
            if (prefId === PLAYER_OPTION_SCROLLABLE) {
                callback = parseInt(prefValue) === SCROLLABLE_DISABLE ? dojo.removeClass : dojo.addClass;
                dojo.query('.factionBoard').forEach((factionBoard) => {
                    callback(factionBoard, 'scrollable');
                });
            }
        },

        notif_locationRuined(n) {
            debug('Notif: locationRuined', n);
            dojo.addClass(`location_${n.args.location.id}`, 'back');
            this.destroyAll(`#location_${n.args.location.id} .resourceIcon`);
        },
    });
});
