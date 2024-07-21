define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.discardCardsGameStart', null, {
        constructor() {},

        onEnteringStateDiscardCardsGameStart(args) {
            debug('DiscardCardsGameStart state', args);
            if (this.isCurrentPlayerActive()) {
                this.addPrimaryActionButton(
                    'buttonDiscardCards',
                    _('Discard 1, 3'),
                    () => this.takeAction('actDiscardCardsGameStart', { ids: [1, 3].join(';') })
                );
                this.addPrimaryActionButton(
                    'buttonDiscardCards2',
                    _('Discard 4, 6'),
                    () => this.takeAction('actDiscardCardsGameStart', { ids: [4, 6].join(';') })
                );
            }
        },
    });
});
