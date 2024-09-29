define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.confirmTurnEnd', null, {
        constructor() {
        },

        onEnteringStateConfirmTurnEnd() {
            if (this.isCurrentPlayerActive()) {
                const buttonId = 'buttonConfirmTurn';
                this.addEndTurnButton(buttonId);
                this.startActionTimer(buttonId, 10);
                this.addSecondaryActionButton('buttonLetMeThink', _('Let me think'), () => {
                    this.stopActionTimer(buttonId);
                    this.removeActionButtons();
                    this.addEndTurnButton(buttonId);
                    this.addResetTurnButton();
                });
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
