define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.specificLocationActions', null, {
        constructor() {
            this._notifications.push(['dealDiscarded', 1]);
        },

        onEnteringStateChooseDealToLose(args) {
            if (this.isCurrentPlayerActive()) {
                args.resources.forEach((resource) => {
                    this.addActionButtonWithResource(resource, 'actChooseDeal', false);
                });
                this.addUndoButton();
            }
        },

        onEnteringStateChooseResourceToSpend(args) {
            if (this.isCurrentPlayerActive()) {
                args.resources.forEach((resource) => {
                    this.addActionButtonWithResource(resource, 'actChooseResourceToSpend');
                });
            }
        },

        notif_dealDiscarded(n) {
            debug('Notif: dealDiscarded', n);
            const dealIcon = `#faction_${n.args.player_id} .deals .${n.args.resourceRemoved}Icon`;
            dojo.destroy(this.querySingle(dealIcon));
            if (dojo.query(dealIcon).length === 0) {
                dojo.destroy(this.querySingle(`#faction_${n.args.player_id} .deals .${n.args.resourceRemoved}Block`));
            }
            this.addLocation(n.args.discarded, this.querySingle(`#faction_${n.args.player_id} .deals`));
            this.runDiscardLocationAnimation(
                n.args.discarded,
                n.args.newDiscardCount,
                n.args.player_id
            );
        },
    });
});
