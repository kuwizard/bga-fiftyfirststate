define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.common', null, {
        constructor() {
            this._notifications.push(['handChanged', 1]);
        },

        forEachFactionRow(callback) {
            ['production', 'feature', 'actions'].forEach(callback);
        },

        getRandomNumber(min, max) {
            return Math.floor(Math.random() * (max - min) + min);
        },

        addSomeRandomMargins(element, marginDelta = 7, rotateDelta = 20) {
            dojo.style(element, 'margin-left', `${this.getRandomNumber(-marginDelta, marginDelta)}px`);
            dojo.style(element, 'margin-top', `${this.getRandomNumber(-marginDelta, marginDelta)}px`);
            dojo.style(element, 'transform', `rotate(${this.getRandomNumber(-rotateDelta, rotateDelta)}deg)`);
        },

        replaceWithResourceIcon(lexeme) {
            if (/{.*}/.test(lexeme)) {
                const match = lexeme.match(/{(.*?Icon)}/);
                const type = match[1].replace(/Icon$/, '');
                return lexeme.replace(/{.*}/, this.format_block('jstpl_resource_icon', { type: type }));
            } else {
                return lexeme;
            }
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
