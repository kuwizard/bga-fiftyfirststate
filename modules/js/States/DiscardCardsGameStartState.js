define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.discardCardsGameStart', null, {
        constructor() {
            this._notifications.push(['handChanged', 1]);
        },

        onEnteringStateDiscardCardsGameStart(args) {
            debug('DiscardCardsGameStart state', args);
            if (this.isCurrentPlayerActive()) {
                this.makeAllSelectableAndClickable(this.getHand(), this.selectLocation.bind(this));
            }
        },

        selectLocation(location) {
            if (location.classList.contains('selected')) {
                dojo.removeClass(location, 'selected');
                const selected = dojo.query('.selected');
                this.clearPossible();
                this.makeAllSelectableAndClickable(this.getHand(), this.selectLocation.bind(this));
                this.addSelectedClass(selected);
            } else {
                this.addSelectedClass(location);
                const selected = dojo.query('.selected');
                if (selected.length === 2) {
                    this.clearPossible();
                    this.makeAllSelectedAndClickable(selected, this.selectLocation.bind(this));
                    this.addUnselectableClass(dojo.query('.location:not(.selected)'));
                    this.addPrimaryActionButton(
                        'buttonDiscardCards',
                        _('Discard selected'),
                        () => this.discardSelected()
                    );
                }
            }
        },

        getHand() {
            return dojo.query('#hand .location');
        },

        discardSelected() {
            const ids = dojo.query('.selected').map(el => this.extractId(el, 'location'));
            this.takeAction('actDiscardCardsGameStart', { ids: ids.join(';') });
        },
    });
});
