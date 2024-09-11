define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.phaseOneLookoutChoose', null, {
        constructor() {
            this._notifications.push(['newConnections', 1]);

        },

        onEnteringStatePhaseOneLookoutChoose(args) {
            debug('phaseOneLookoutChoose state', args);
            dojo.style('lookout', 'display', 'flex');
            dojo.style('connections', 'display', 'none');
            this.forEachPlayer((player) => {
                dojo.removeClass(`overall_player_board_${player.id}`, 'passed');
            });
            this.waitForDisappearance('.moving').then(() => {
                this.destroyAll('#lookout .location');
                args.forEach((location) => {
                    const locationElement = this.addLocation(location, $('lookout'));
                    if (this.isCurrentPlayerActive()) {
                        this.addSelectableClass(locationElement);
                        this.dojoConnect(
                            locationElement,
                            () => this.clickLocationLookout(this.extractId(locationElement, 'location'))
                        );
                    }
                });
            });
        },

        onEnteringStatePhaseTwoProduction() {
            this.waitForDisappearance('.moving').then(() => {
                dojo.style('lookout', 'display', 'none');
                dojo.style('connections', 'display', 'flex');
            });
        },

        clickLocationLookout(id) {
            this.takeAction('actChooseCardLookout', { id: id })
        },

        notif_newConnections(n) {
            debug('Notif: newConnections', n);
            this.destroyAll('#connections .connection');
            this.addConnections(n.args.connections);
        },
    });
});
