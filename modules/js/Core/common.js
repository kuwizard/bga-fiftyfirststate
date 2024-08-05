define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.common', null, {
        constructor() {
            this._notifications.push(['handChanged', 1]);
        },

        notif_handChanged(n) {
            debug('Notif: handChanged', n);
            this.destroyAll('#hand .location');
            n.args.hand.forEach((location) => {
                dojo.place(this.format_block('jstpl_location', location), 'hand');
            });
        },
    });
});
