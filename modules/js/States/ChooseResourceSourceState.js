define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.chooseResourceSource', null, {
        constructor() {
            this._notifications.push(['resourcesLocationChanged', 1]);
        },

        onEnteringStateChooseResourceSource(args) {
            debug('ChooseResourceSource state', args);
            if (this.isCurrentPlayerActive()) {
                if (args.sources.faction) {
                    this.addPrimaryActionButton(
                        'buttonChooseSourceFaction',
                        _('Faction'),
                        () => this.takeAction('actChooseSource', { id: 0 })
                    );
                }
                if (args.sources.locations) {
                    args.sources.locations.forEach((locationId) => {
                        this.addPrimaryActionButton(
                            `buttonChooseSource${locationId}`,
                            locationId,
                            () => this.takeAction('actChooseSource', { id: locationId })
                        );
                    });
                }
                if (args.sources.joker) {
                    this.addPrimaryActionButton(
                        'buttonChooseSourceJoker',
                        this.replaceWithResourceIcon(_('Use {joker} instead').replace(
                            'joker',
                            `${args.sources.jokerIcon}Icon`
                        )),
                        () => this.takeAction('actChooseSource', { id: args.sources.joker })
                    );
                    dojo.addClass('buttonChooseSourceJoker', 'resourceButton');
                }
            }
        },

        notif_resourcesLocationChanged(n) {
            debug('Notif: resourcesLocationChanged', n);
            // TODO: Send new amount from server and support it here
            dojo.destroy(this.querySingle(`#location_${n.args.locationId} .resources .${n.args.resourceName}Icon`));
        },
    });
});
