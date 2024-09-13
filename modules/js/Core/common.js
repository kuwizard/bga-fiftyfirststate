define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.common', null, {
        constructor() {
            this._notifications.push(['handChanged', 1]);
            this._notifications.push(['deckChanged', 1]);
            this._notifications.push(['locationDiscarded', 1]);
            this._notifications.push(['locationPicked', 1]);
            this._notifications.push(['lastRound', 1]);
            this._notifications.push(['reshuffle', 1]);
            this._notifications.push(['message', 1]);
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
            location.additionalClass = location.isRuined ? ' ruined' : '';
            return location;
        },

        addDeckConnectionsElement(gamedatas) {
            dojo.place(this.format_block('jstpl_deck_connections', {}), 'board');
            dojo.place(this.format_block('jstpl_header', { text: _('Deck: '), value: gamedatas.deck }), 'deckHeader');
            dojo.place(
                this.format_block('jstpl_header', { text: _('Discard: '), value: gamedatas.discard }),
                'discardHeader'
            );
            dojo.place(
                this.format_block('jstpl_collapsed_text', { text: _('Expand this to see Connections') }),
                'connections'
            );
            dojo.place(this.format_block(
                'jstpl_collapsed_text',
                { text: _('Expand this to see cards to choose from') }
            ), 'lookout');
            this.addLocation({ isRuined: true }, $('deck'));
            if (gamedatas.discardLastLocation === null) {
                gamedatas.discardLastLocation = {};
            }
            const discardTop = this.addLocation(gamedatas.discardLastLocation, $('discard'));
            if (!gamedatas.discardLastLocation.id) {
                dojo.addClass(discardTop, 'placeholder');
            }
            this.addConnections(gamedatas.connections);
        },

        addConnections(connections) {
            connections.forEach((connection) => {
                if (connection === null) {
                    connection = { id: 0, sprite: 0, additionalClass: ' flipped' }
                } else {
                    connection = { ...connection, additionalClass: '' }
                }
                const connectionBlock = this.format_block('jstpl_connection', connection);
                dojo.place(connectionBlock, 'connections');
                if (connection.id !== 0) {
                    this.addTooltipHtml(`connection_${connection.id}`, connectionBlock);
                }
            });
        },

        addLastRound() {
            // 11 should be a calculated parameter but when you use 1-5 - it's placed as a second child. Bug?..
            dojo.place(
                this.format_block('jstpl_last_round', { text: _('This is the last round!') }),
                'game_play_area',
                11
            );
        },

        addActionButtonWithResource(resource, action) {
            this.addPrimaryActionButton(
                `buttonStore${resource}`,
                this.format_block('jstpl_resource_icon', { type: resource }),
                () => this.takeAction(action, { resource: resource })
            );
            dojo.addClass(`buttonStore${resource}`, 'resourceButton');
        },

        addLocation(location, destination, isFirst = false) {
            const locationBlock = this.format_block('jstpl_location', this.enrichLocationObject(location));
            const locationElement = dojo.place(locationBlock, destination, isFirst ? 'first' : 'last');
            this.addTooltipHtml(`location_${location.id}`, locationBlock);
            return locationElement;
        },

        addEventListenerToResize() {
            window.addEventListener('resize', function () {
                this.setCorrectClassToOverlapHand();
            }.bind(this), true);
        },

        setCorrectClassToOverlapHand() {
            const images = dojo.query('#hand .locationImage').map((image) => {
                return image.offsetWidth
            });
            const sum = images.reduce((partialSum, a) => partialSum + a, 0) + (images.length - 1) * 5;
            if (this.querySingle('#hand').offsetWidth <= sum) {
                dojo.removeClass('hand', 'notTooManyChildren');
            } else {
                dojo.addClass('hand', 'notTooManyChildren');
            }
        },

        async notif_handChanged(n) {
            debug('Notif: handChanged', n);
            await this.waitForDisappearance('.moving');
            this.destroyAll('#hand .location');
            const elements = n.args.hand.map((location) => {
                return this.addLocation(location, $('hand'));
            });
            this.setMagicLocationClasses(elements);
            this.setCorrectClassToOverlapHand()
        },

        notif_deckChanged(n) {
            debug('Notif: deckChanged', n);
            this.querySingle(`#deckHeader .headerValue`).innerText = n.args.deckCount;
        },

        notif_locationPicked(n) {
            debug('Notif: locationPicked', n);
            if (n.args.source === 'lookout') {
                if (n.args.player_id === this.player_id) {
                    this.slide(`location_${n.args.location.id}`, 'hand', { phantom: true });
                } else {
                    this.slide(
                        `location_${n.args.location.id}`,
                        `overall_player_board_${n.args.player_id}`,
                        { destroy: true }
                    );
                }
            }
            const locationBlock = this.format_block('jstpl_location', this.enrichLocationObject(n.args.location));
            this.addTooltipHtml(this.querySingle('.log .locationName'), locationBlock);
        },

        notif_reshuffle(n) {
            debug('Notif: reshuffle', n);
            this.querySingle(`#deckHeader .headerValue`).innerText = n.args.deckCount;
            this.querySingle(`#discardHeader .headerValue`).innerText = n.args.discardCount;
            dojo.addClass(this.querySingle(`#discard .location`), 'placeholder');
        },

        async notif_locationDiscarded(n) {
            debug('Notif: locationDiscarded', n);
            await this.waitForDisappearance('.moving');
            dojo.query(`#location_${n.args.location.id} .resourceIcon`).forEach((element) => {
                const resourceType = [...element.classList].find((clazz) => {
                    return clazz !== 'resourceIcon'
                }).replace('Icon', '');
                this.slide(
                    element,
                    this.querySingle(`#overall_player_board_${n.args.player_id} .${resourceType}`),
                    { destroy: true }
                );
            });
            this.runDiscardLocationAnimation(n.args.location, n.args.newDiscardCount, n.args.player_id);
            this.setMagicLocationClasses(dojo.query('#hand .locationWrapper'));
        },

        async runDiscardLocationAnimation(location, newDiscardCount, playerId) {
            let locationElement = $(`location_${location.id}`);
            if (!locationElement) {
                locationElement = this.addLocation(location, $(`overall_player_board_${playerId}`), true);
            }
            const discardIsCollapsed = this.querySingle('#deckConnectionsBlock.collapsed');
            if (discardIsCollapsed) {
                await this.slide(locationElement, 'discardHeader', { destroy: false });
                this.changeParent(locationElement, 'discard', true);
            } else {
                await this.slide(locationElement, 'discard');
            }
            this.destroyAll(`#discard .location:not(#location_${location.id})`);
            this.querySingle(`#discardHeader .headerValue`).innerText = newDiscardCount;
        },

        // Pure hacks to make sure locations are sequentially overlapped on small screens
        setMagicLocationClasses(locations) {
            locations.forEach((location) => {
                dojo.attr(location, 'data-items', locations.length);
            });
        },

        notif_lastRound(n) {
            debug('Notif: lastRound', n);
            this.addLastRound();
        },

        notif_message(n) {
            debug('Notif: message', n);
        },
    });
});
