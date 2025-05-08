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
                    args.sources.locations.forEach((location) => {
                        this.addPrimaryActionButton(
                            `buttonChooseSource${location.id}`,
                            _(location.name),
                            () => this.takeAction('actChooseSource', { id: location.id })
                        );
                    });
                }
                if (args.sources.locationsWithJoker) {
                    args.sources.locationsWithJoker.forEach((location) => {
                        this.addPrimaryActionButton(
                            `buttonChooseSource${location.id}`,
                            this.replaceWithResourceIcon(`${_(location.name)} ({ammoIcon})`, true),
                            () => this.takeAction('actChooseSource', { id: location.id })
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
                this.addUndoButton();
            }
        },

        notif_resourcesLocationChanged(n) {
            debug('Notif: resourcesLocationChanged', n);
            // TODO: Send new amount from server and support it here
            dojo.destroy(this.querySingle(`#location_${n.args.locationId} .resources .${n.args.resourceName}Icon`));
        },
    });
});
