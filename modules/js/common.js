define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.common', null, {
        constructor() {
            this._notifications.push(['handChanged', 1]);
            this._notifications.push(['deckChanged', 1]);
            this._notifications.push(['locationDiscarded', 1]);
            this._notifications.push(['locationPicked', 1]);
            this._notifications.push(['lastRound', 1]);
            this._notifications.push(['endOfGameVPGained', 1]);
            this._notifications.push(['locationsReshuffle', 1]);
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

        replaceWithResourceIcon(lexeme, isLogIcon = false) {
            if (/{.*}/.test(lexeme)) {
                while (/{.*}/.test(lexeme)) {
                    const match = lexeme.match(/{(.*?Icon)}/);
                    const type = match[1].replace(/Icon$/, '');
                    const tpl = isLogIcon ? 'jstpl_resource_icon_log' : 'jstpl_resource_icon';
                    lexeme = lexeme.replace(/{.*?}/, this.format_block(tpl, { type: type }));
                }
            }
            return lexeme;
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
            this.placeText('jstpl_header', "{l}: ".replace('{l}', this.getDeckLexeme()), 'deckHeader', gamedatas.deck);
            this.placeText(
                'jstpl_header',
                "{l}: ".replace('{l}', this.getDiscardLexeme()),
                'discardHeader',
                gamedatas.discard
            );
            this.placeText('jstpl_collapsed_text', this.getExpandConnectionsLexeme(), 'connections');
            this.placeText('jstpl_collapsed_text', this.getExpandLocationsLexeme(), 'lookout');
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
                this.addConnection(connection, 'connections');
            });
        },

        addConnection(connection, destination) {
            if (connection === null) {
                connection = { id: 0, sprite: 0, additionalClass: ' flipped' }
            } else {
                connection = { ...connection, additionalClass: '' }
            }
            const connectionBlock = this.format_block('jstpl_connection', connection);
            dojo.place(connectionBlock, destination);
            if (connection.id !== 0) {
                this.addTooltipHtml(`connection_${connection.id}`, connectionBlock);
            }
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
            const locationNotRuinedBlock = this.format_block(
                'jstpl_location',
                this.enrichLocationObject({ ...location, isRuined: false })
            );
            this.addTooltipHtml(`location_${location.id}`, locationNotRuinedBlock);
            return locationElement;
        },

        addEventListenerToResize() {
            window.addEventListener('resize', function () {
                this.setCorrectClassToOverlapHand();
            }.bind(this), true);
        },

        setCorrectClassToOverlapHand() {
            const images = dojo.query('#handLocations .locationImage').map((image) => {
                return image.offsetWidth;
            });
            const sum = images.reduce((partialSum, a) => partialSum + a, 0) + (images.length - 1) * 5;
            if (this.querySingle('#handLocations').offsetWidth <= sum) {
                dojo.removeClass('handLocations', 'notTooManyChildren');
            } else {
                dojo.addClass('handLocations', 'notTooManyChildren');
            }
        },

        async notif_handChanged(n) {
            debug('Notif: handChanged', n);
            await this.waitForDisappearance('.moving');
            this.destroyAll('#handLocations .location');
            n.args.hand.map((location) => {
                return this.addLocation(location, $('handLocations'));
            });
            this.setCorrectClassToOverlapHand();
        },

        notif_deckChanged(n) {
            debug('Notif: deckChanged', n);
            this.querySingle(`#deckHeader .headerValue`).innerText = n.args.deckCount;
        },

        async notif_locationPicked(n) {
            debug('Notif: locationPicked', n);
            await this.waitForDisappearance('.moving');
            if (n.args.source === 'lookout') {
                if (n.args.player_id === this.player_id) {
                    await this.slide(
                        `location_${n.args.location.id}`,
                        'handLocations',
                        { phantom: true, targetPos: n.args.newPosition }
                    );
                    this.addClass(`location_${n.args.location.id}`, 'justPickedSlide', true, 2000);
                } else {
                    this.slide(
                        `location_${n.args.location.id}`,
                        `overall_player_board_${n.args.player_id}`,
                        { destroy: true }
                    );
                }
            }
            this.addTooltipToLogEntry(n.args.location);
        },

        notif_locationsReshuffle(n) {
            debug('Notif: reshuffle', n);
            this.querySingle(`#deckHeader .headerValue`).innerText = n.args.deckCount;
            this.querySingle(`#discardHeader .headerValue`).innerText = n.args.discardCount;
            dojo.addClass(this.querySingle(`#discard .location`), 'placeholder');
        },

        async notif_locationDiscarded(n) {
            debug('Notif: locationDiscarded', n);
            await this.waitForDisappearance('.moving');

            dojo.query(`#location_${n.args.location.id} .resourceIcon`).forEach((element, index) => {
                if (n.args.discardResources) {
                    const resourceType = [...element.classList].find((clazz) => {
                        return clazz !== 'resourceIcon'
                    }).replace('Icon', '');
                    this.slide(
                        element,
                        this.querySingle(`#overall_player_board_${n.args.player_id} .${resourceType}`),
                        { delay: index * 70, destroy: true }
                    );
                } else {
                    dojo.destroy(element);
                }
            });
            await this.waitForDisappearance('.moving');
            this.runDiscardLocationAnimation(n.args.location, n.args.newDiscardCount, n.args.player_id);
            this.addTooltipToLogEntry(n.args.location);
            this.setCorrectClassToOverlapHand();
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
                dojo.removeClass(locationElement, 'ruined');
            }
            this.destroyAll(`#discard .location:not(#location_${location.id})`);
            this.querySingle(`#discardHeader .headerValue`).innerText = newDiscardCount;
        },

        addTooltipToLogEntry(location) {
            const locationBlock = this.format_block('jstpl_location', this.enrichLocationObject(location));
            this.addTooltipHtml(this.querySingle('.log .locationName'), locationBlock);
        },

        notif_lastRound(n) {
            debug('Notif: lastRound', n);
            this.addLastRound();
        },

        notif_message(n) {
            debug('Notif: message', n);
        },

        notif_endOfGameVPGained(n) {
            debug('Notif: endOfGameVPGained', n);
            this.scoreCtrl[n.args.player_id].toValue(n.args.total);
        },
    });
});
