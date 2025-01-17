define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.common', null, {
        constructor() {
            this._notifications.push(['locationsDrawn', 1]);
            this._notifications.push(['deckChanged', 1]);
            this._notifications.push(['locationDiscarded', 1]);
            this._notifications.push(['locationPicked', 1]);
            this._notifications.push(['lastRound', 1]);
            this._notifications.push(['endOfGameVPGained', 1]);
            this._notifications.push(['locationsReshuffle', 1]);
            this._notifications.push(['removeLastRound', 1]);
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
            if (/{.*?[IA]con}/.test(lexeme)) {
                while (/{.*?[IA]con}/.test(lexeme)) {
                    const match = lexeme.match(/{(.*?[IA]con)}/);
                    const type = match[1].replace(/[IA]con$/, '');
                    let tpl = isLogIcon ? 'jstpl_resource_icon_log' : 'jstpl_resource_icon';
                    if (lexeme.match(/[IA]con}/)[0] === 'Acon}') {
                        tpl = 'jstpl_resource_acon_log';
                    }
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
            location.additionalClass = location.isRuined ? ' back' : '';
            return location;
        },

        addBoard() {
            dojo.place(this.format_block('jstpl_board', {}), 'game_play_area');
            dojo.place(this.format_block('jstpl_arrow_up', {}), 'toTop');
            dojo.connect(this.querySingle('#toTop'), 'click', () => {
                this.scrollToTop();
            });
        },

        addDeckConnectionsElement(gamedatas) {
            dojo.place(this.format_block('jstpl_deck_connections', {}), 'board');
            dojo.place(this.format_block('jstpl_arrow_up', {}), 'collapseButton');
            this.placeText('jstpl_header', "{l}: ".replace('{l}', this.getDeckLexeme()), 'deckHeader', gamedatas.deck);
            this.placeText(
                'jstpl_header',
                "{l}: ".replace('{l}', this.getDiscardLexeme()),
                'discardHeader',
                gamedatas.discard
            );
            this.placeText('jstpl_collapsed_text', this.getExpandConnectionsLexeme(), 'connections', '', true);
            this.placeText('jstpl_collapsed_text', this.getExpandLocationsLexeme(), 'lookout');
            this.addLocation({ isRuined: true }, $('deck'));
            if (gamedatas.discardLastLocation === null) {
                gamedatas.discardLastLocation = {};
            }
            const discardTop = this.addLocation(gamedatas.discardLastLocation, $('discard'));
            if (!gamedatas.discardLastLocation.id) {
                dojo.addClass(discardTop, 'placeholder');
            }
            const connectionsHeader = this.replaceWithResourceIcon(this.getConnectionsSpendLexeme(), true);
            this.placeText('jstpl_header', connectionsHeader, 'connections', '', true);
            this.addConnections(gamedatas.connections);
        },

        addConnections(connections) {
            connections.forEach((connection) => {
                this.addConnection(connection, 'connectionsCards');
            });
        },

        addConnection(connection, destination) {
            if (connection === null) {
                connection = { id: 0, sprite: 0, additionalClass: ' back' }
            } else {
                connection = { ...connection, additionalClass: '' }
            }
            let connectionBlock = this.format_block('jstpl_connection', connection);
            dojo.place(connectionBlock, destination);
            if (connection.id !== 0) {
                connectionBlock = connectionBlock + this.getConnectionText(connection.text, connection.name);
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

        addActionButtonWithResource(resource, action, wrapIntoConfirmation = true) {
            this.addPrimaryActionButton(
                `buttonStore${resource}`,
                this.format_block('jstpl_resource_icon', { type: resource }),
                () => {
                    this.wrapIntoCardConfirmation(
                        () => this.takeAction(action, { resourceName: resource }),
                        wrapIntoConfirmation && resource === 'card',
                    )();
                }
            );
            dojo.addClass(`buttonStore${resource}`, 'resourceButton');
        },

        wrapIntoConfirmation(lexeme, callback, condition = true) {
            if (condition) {
                return () => this.confirmationDialog(lexeme, () => {
                    callback();
                });
            } else {
                return () => callback();
            }
        },

        addLocation(location, destination, isFirst = false) {
            const locationBlock = this.format_block('jstpl_location', this.enrichLocationObject(location));
            const locationElement = dojo.place(locationBlock, destination, isFirst ? 'first' : 'last');
            let locationNotRuinedBlock = this.format_block(
                'jstpl_location',
                this.enrichLocationObject({ ...location, isRuined: false })
            );
            if (location.text) {
                locationNotRuinedBlock = locationNotRuinedBlock + this.getLocationText(location.text, location.name);
                this.addTooltipHtml(`location_${location.id}`, locationNotRuinedBlock);
            }
            return locationElement;
        },

        addEventListenerToResize() {
            window.addEventListener('resize', function () {
                this.setCorrectClassToOverlapCards();
            }.bind(this), true);
        },

        setCorrectClassToOverlapCards() {
            let singleWidth = this.querySingle('.location').offsetWidth;
            if (singleWidth < 169) {
                singleWidth = 169;
            }
            // Hand
            if (!this.isSpectator) {
                const handImages = dojo.query('#handLocations .locationImage');
                const handSum = singleWidth * handImages.length + (handImages.length - 1) * 5;
                this.setOrRemoveClass(
                    'handLocations',
                    this.querySingle('#handLocations').offsetWidth > handSum,
                    'notTooManyChildren'
                );
            }

            // Any other row
            dojo.query('.cardsBlock').forEach((row) => {
                const rowSum = singleWidth * row.childElementCount + (row.childElementCount - 1) * 4;
                this.setOrRemoveClass(row, row.offsetWidth > rowSum, 'notTooManyChildren');
            });

            // Lookout
            const lookout = this.querySingle('#lookout');
            if (!lookout.classList.contains('hidden')) {
                const locations = dojo.query('#lookout .location');
                const lookoutWidth = singleWidth * locations.length + (locations.length - 1) * 5;
                this.setOrRemoveClass(lookout, window.innerWidth * 0.7 > lookoutWidth, 'notTooManyChildren');
            }
            // 1366 is the iPad Pro width. However for 4 players even this number is too small
            const width = Object.keys(this.gamedatas.players).length === 4 ? 1500 : 1366;
            this.setOrRemoveClass('deckConnectionsBlock', window.innerWidth <= width, 'narrow');
        },

        setOrRemoveClass(element, condition, clazz) {
            if (condition) {
                dojo.addClass(element, clazz);
            } else {
                dojo.removeClass(element, clazz);
            }
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
                dojo.removeClass(locationElement, 'back');
            }
            this.destroyAll(`#discard .location:not(#location_${location.id})`);
            this.querySingle(`#discardHeader .headerValue`).innerText = newDiscardCount;
        },

        addTooltipToLogEntry(location) {
            const locationBlock = this.format_block('jstpl_location', this.enrichLocationObject(location));
            this.addTooltipHtml(this.querySingle('.log .locationName'), locationBlock);
        },

        setPlayerPass(playerId) {
            if (playerId === this.player_id && this.resourceCounters.sticky) {
                console.log('***debug: set sticky passed');
                dojo.addClass('sticky', 'passed');
                console.log('***debug: set sticky passed done');
            } else {
                console.log('***debug: set overall_player_board_${playerId} passed');
                dojo.addClass(`overall_player_board_${playerId}`, 'passed');
                console.log('***debug: set overall_player_board_${playerId} done');
            }
            this.gamedatas.players[playerId].passed = true;
        },

        setPlayerUnpass(playerId) {
            if (playerId === this.player_id && this.resourceCounters.sticky) {
                console.log('***debug: set sticky unpassed');
                dojo.removeClass('sticky', 'passed');
                console.log('***debug: set sticky unpassed done');
            } else {
                console.log('***debug: set `overall_player_board_${playerId}` unpassed');
                dojo.removeClass(`overall_player_board_${playerId}`, 'passed');
                console.log('***debug: set `overall_player_board_${playerId}` unpassed done');
            }
            this.gamedatas.players[playerId].passed = false;
        },

        collapseConnectionsBlock() {
            dojo.toggleClass('deckConnectionsBlock', 'collapsing');
            setTimeout(() => { // We want to hide cards with a slight delay
                dojo.toggleClass('deckConnectionsBlock', 'collapsing');
                dojo.toggleClass('deckConnectionsBlock', 'collapsed');
            }, 100);
        },

        scrollToPlayerFaction(pId) {
            this.querySingle(`#faction_${pId}`).scrollIntoView({ behavior: "smooth", block: "center" })
        },

        scrollToTop() {
            this.querySingle(`#topbar`).scrollIntoView({ behavior: "smooth" })
        },

        async notif_locationsDrawn(n) {
            debug('Notif: locationsDrawn', n);
            await this.waitForDisappearance('.moving, .turnAround');
            for (const position of Object.keys(n.args.new)) {
                if (n.args.disableAnimation) {
                    this.addLocation(n.args.new[position], $('handLocations'));
                } else {
                    this.addLocation(n.args.new[position], $('deckBlock'), true);
                    this.slide(
                        `location_${n.args.new[position].id}`,
                        'handLocations',
                        { phantomEnd: true, targetPos: parseInt(position) }
                    );
                    this.addClass(`location_${n.args.new[position].id}`, 'justPicked', true, 3000);
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
            }
            this.setCorrectClassToOverlapCards();
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
            dojo.addClass('board', 'discarding');
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
            await this.runDiscardLocationAnimation(n.args.location, n.args.newDiscardCount, n.args.player_id);
            this.addTooltipToLogEntry(n.args.location);
            this.setCorrectClassToOverlapCards();
            dojo.removeClass('board', 'discarding');
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

        notif_removeLastRound(n) {
            debug('Notif: removeLastRound', n);
            dojo.destroy('lastRound');
        },
    });
});
