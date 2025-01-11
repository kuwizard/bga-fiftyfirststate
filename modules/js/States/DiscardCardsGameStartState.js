define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.discardCardsGameStart', null, {
        constructor() {
        },

        onEnteringStateDiscardCardsGameStart(args) {
            // Let's wait for a tiny bit until gameStateMultipleActiveUpdate arrives and makes current player active
            setTimeout(() => {
                if (this.isCurrentPlayerActive()) {
                    this.makeAllSelectableAndClickable(this.getHand(), this.selectLocation.bind(this));
                }
            }, 1);
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
            const locationsIds = dojo.query('.selected').map(el => this.extractId(el, 'location'));
            this.takeAction('actDiscardCardsGameStart', { locationsIds: locationsIds.join(';') });
        },
    });
});
