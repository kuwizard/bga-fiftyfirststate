define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.choosePlayerToSteal', null, {
        constructor() {
        },

        onEnteringStateChoosePlayerToSteal(args) {
            if (this.isCurrentPlayerActive()) {
                args.players.forEach((player) => {
                    this.addPrimaryActionButton(
                        `buttonPlayer${player.playerId}`,
                        player.playerName,
                        () => this.takeAction('actChoosePlayerToSteal', { pId: player.playerId })
                    );
                    this.selectResourcesToSteal(player.playerId, player.resources, 'actChoosePlayerAndResourceToSteal');
                });
                this.addUndoButton();
            }
        },

        onEnteringStateChooseResourceToSteal(args) {
            if (this.isCurrentPlayerActive()) {
                this.selectResourcesToSteal(args.playerId, args.resources, 'actChooseResourceToSteal');
                args.resources.forEach((resource) => {
                    this.addActionButtonWithResource(resource, 'actChooseResourceToSteal');
                });
                this.addUndoButton();
            }
        },

        selectResourcesToSteal(playerId, resources, action) {
            dojo.query(`#overall_player_board_${playerId} .resource`).forEach((resourceElement) => {
                const resource = [...resourceElement.classList].find((resource) => {
                    return resources.includes(resource);
                });
                if (resource) {
                    this.addSelectableClass(resourceElement);
                    this.dojoConnect(resourceElement, () => {
                        this.takeAction(
                            action,
                            { pId: playerId, resource: resource }
                        );
                    });
                }
            });
        },
    });
});
