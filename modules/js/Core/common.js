define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.common', null, {
        constructor() {
            this._notifications.push(['handChanged', 1]);
            this._notifications.push(['locationDiscarded', 1]);
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

        enrichLocationObject(location = {}) {
            const defaultValues = {
                id: 0,
                sprite: 0,
                additionalClass: '',
            };
            location = { ...defaultValues, ...location };
            location.additionalClass = location.isRuined || location.id === 0 ? ' ruined' : '';
            return location;
        },

        addDeckConnectionsElement(gamedatas) {
            dojo.place(this.format_block('jstpl_deck_connections', {}), 'board');
            dojo.place(this.format_block('jstpl_header', { text: _('Deck: '), value: gamedatas.deck }), 'deckHeader');
            dojo.place(
                this.format_block('jstpl_header', { text: _('Discard: '), value: gamedatas.discard }),
                'discardHeader'
            );
            dojo.place(this.format_block('jstpl_location', this.enrichLocationObject()), 'deck');
            if (gamedatas.discardLastLocation === null) {
                gamedatas.discardLastLocation = {};
            }
            dojo.place(
                this.format_block('jstpl_location', this.enrichLocationObject(gamedatas.discardLastLocation)),
                'discard'
            );
            this.addConnections(gamedatas.connections);
        },

        addConnections(connections) {
            connections.forEach((connection) => {
                if (connection === null) {
                    connection = { id: 0, sprite: 0, additionalClass: ' flipped' }
                } else {
                    connection = { ...connection, additionalClass: '' }
                }
                dojo.place(
                    this.format_block('jstpl_connection', connection),
                    'connections'
                );
            });
        },

        notif_handChanged(n) {
            debug('Notif: handChanged', n);
            this.destroyAll('#hand .location');
            n.args.hand.forEach((location) => {
                dojo.place(this.format_block('jstpl_location', this.enrichLocationObject(location)), 'hand');
            });
        },

        notif_locationDiscarded(n) {
            debug('Notif: locationDiscarded', n);
            this.slide(`location_${n.args.id}`, 'discard').then(() => {
                this.destroyAll(`#discard .location:not(#location_${n.args.id})`);
                this.querySingle(`#discardHeader .headerValue`).innerText = n.args.newDiscardCount;
            });
        },
    });
});
