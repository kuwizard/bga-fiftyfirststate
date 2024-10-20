define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseOneLookoutChoose', null, {
        constructor() {
            this._notifications.push(['newConnections', 1]);
            this._notifications.push(['firstPlayerChanged', 1]);
        },

        async onEnteringStatePhaseOneLookoutChoose(args) {
            debug('phaseOneLookoutChoose state', args);
            dojo.removeClass('lookout', 'hidden');
            dojo.addClass('connections', 'hidden');
            this.forEachPlayer((player) => {
                dojo.removeClass(`overall_player_board_${player.id}`, 'passed');
                this.gamedatas.players[player.id].passed = false;
            });
            await this.waitForDisappearance('.moving');
            this.destroyAll('#lookout .location');
            args.locations.forEach((location) => {
                const locationElement = this.addLocation(location, $('lookout'));
                if (this.isCurrentPlayerActive()) {
                    this.addSelectableClass(locationElement);
                    this.dojoConnect(
                        locationElement,
                        () => this.clickLocationLookout(this.extractId(locationElement, 'location'))
                    );
                }
            });
            this.querySingle(`#deckHeader .headerValue`).innerText = args.deckCount;
            this.keepLookoutUncollapsable();
            this.setCorrectClassToOverlapCards();
        },

        async onEnteringStatePhaseTwoProduction() {
            await this.waitForDisappearance('.moving')
            dojo.addClass('lookout', 'hidden');
            dojo.removeClass('connections', 'hidden');
        },

        clickLocationLookout(id) {
            this.takeAction('actChooseCardLookout', { id: id })
        },

        keepLookoutUncollapsable() {
            dojo.removeClass('deckConnectionsBlock', 'collapsed');
            this.dojoConnect(
                'collapseButton',
                () => {
                    setTimeout(() => {
                        dojo.removeClass('deckConnectionsBlock', 'collapsed');
                    }, 101);
                }
            );
        },

        notif_newConnections(n) {
            debug('Notif: newConnections', n);
            this.destroyAll('#connections .connection');
            this.addConnections(n.args.connections);
        },

        notif_firstPlayerChanged(n) {
            debug('Notif: firstPlayerChanged', n);
            this.slide('firstPlayer', this.querySingle(`#player_board_${n.args.player_id} .firstPlayerWrapper`));
        },
    });
});
