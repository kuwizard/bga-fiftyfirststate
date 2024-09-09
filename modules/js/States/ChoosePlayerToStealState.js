define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.choosePlayerToSteal', null, {
        constructor() {
        },

        onEnteringStateChoosePlayerToSteal(args) {
            if (this.isCurrentPlayerActive()) {
                args.forEach((item) => {
                    this.addPrimaryActionButton(
                        `buttonPlayer${item.playerId}`,
                        item.playerName,
                        () => this.takeAction('actChoosePlayerToSteal', { pId: item.playerId })
                    );
                    this.selectResourcesToSteal(item.playerId, item.resources, 'actChoosePlayerAndResourceToSteal');
                });
                this.addUndoButton();
            }
        },

        onEnteringStateChooseResourceToSteal(args) {
            if (this.isCurrentPlayerActive()) {
                this.selectResourcesToSteal(args.playerId, args.resources, 'actChooseResourceToSteal');
                args.resources.forEach((resource) => {
                    this.addPrimaryActionButton(
                        `buttonChoose${resource}`,
                        this.format_block('jstpl_resource_icon', { type: resource }),
                        () => this.takeAction('actChooseResourceToSteal', { resource: resource })
                    );
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
