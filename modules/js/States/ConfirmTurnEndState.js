define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    const PLAYER_OPTION_TIMER = 401;
    const TIMER_10_SECONDS = 0;
    return declare('state.confirmTurnEnd', null, {
        constructor() {
        },

        onEnteringStateConfirmTurnEnd(args) {
            if (this.isCurrentPlayerActive()) {
                const buttonId = 'buttonConfirmTurn';
                this.addEndTurnButton(buttonId);
                if (this.getGameUserPreference(PLAYER_OPTION_TIMER) === TIMER_10_SECONDS || args.forceTimer) {
                    const seconds = args.forceTimer ? args.forceTimer : 10;
                    this.startActionTimer(buttonId, seconds);
                    this.addSecondaryActionButton('buttonLetMeThink', _('Let me think'), () => {
                        this.stopActionTimer(buttonId);
                        this.removeActionButtons();
                        this.addEndTurnButton(buttonId);
                        if (args.mayPlaceDefence) this.addPlaceDefenceButton();
                        this.addResetTurnButton();
                    });
                }
                if (args.mayPlaceDefence) this.addPlaceDefenceButton();
                this.addResetTurnButton();
            }
        },

        addEndTurnButton(buttonId) {
            this.addPrimaryActionButton(buttonId, _('End turn'), () =>
                this.takeAction('actConfirmTurnEnd', {})
            );
        },

        addResetTurnButton() {
            this.addDangerActionButton('buttonResetTurn', _('Reset turn'), () => {
                this.takeAction('actResetTurn', {});
            });
        },
    });
});
