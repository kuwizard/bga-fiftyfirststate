define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.discardCardsGameStart', null, {
        constructor() {
        },

        onEnteringStateDiscardCardsGameStart(args) {
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
                    this.addUnselectableClass(dojo.query('#handLocations .location:not(.selected)'));
                    this.addPrimaryActionButton(
                        'buttonDiscardCards',
                        _('Discard selected'),
                        () => {
                            this.discardSelected();
                            this.clearPossible();
                        }
                    );
                }
            }
        },

        getHand() {
            return dojo.query('#handLocations .location');
        },

        discardSelected() {
            const ids = dojo.query('.selected').map(el => this.extractId(el, 'location'));
            this.takeAction('actDiscardCardsGameStart', { ids: ids.join(';') });
        },
    });
});
