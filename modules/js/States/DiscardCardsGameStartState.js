define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.discardCardsGameStart', null, {
        constructor() {
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

        makeAllSelectableAndClickable(elements, callback) {
            this.addSelectableClass(elements);
            elements.forEach((element) => {
                this.dojoConnect(element, () => callback(element));
            });
        },

        makeAllSelectedAndClickable(elements, callback) {
            this.addSelectedClass(elements);
            elements.forEach((element) => {
                this.dojoConnect(element, () => callback(element));
            });
        },

        discardSelected() {
            const ids = dojo.query('.selected').map(el => this.extractId(el, 'location'));
            debugger;
            this.takeAction('actDiscardCardsGameStart', { ids: ids.join(';') });
        },
    });
});
