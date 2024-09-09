define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.chooseDealToLose', null, {
        constructor() {
            this._notifications.push(['dealDiscarded', 1]);
        },

        onEnteringStateChooseDealToLose(args) {
            if (this.isCurrentPlayerActive()) {
                args.forEach((resource) => {
                    this.addActionButtonWithResource(resource, 'actChooseDeal');
                });
                this.addUndoButton();
            }
        },

        notif_dealDiscarded(n) {
            debug('Notif: dealDiscarded', n);
            const dealIcon = `#faction_${n.args.player_id} .deals .${n.args.resourceRemoved}Icon`;
            dojo.destroy(this.querySingle(dealIcon));
            if (dojo.query(dealIcon).length === 0) {
                dojo.destroy(this.querySingle(`#faction_${n.args.player_id} .deals .${n.args.resourceRemoved}Block`));
            }
            dojo.place(this.format_block(
                'jstpl_location',
                this.enrichLocationObject(n.args.discarded)
            ), this.querySingle(`#faction_${n.args.player_id} .deals`));
            this.runDiscardLocationAnimation(`location_${n.args.discarded.id}`, n.args.newDiscardCount)
        }
    });
});
