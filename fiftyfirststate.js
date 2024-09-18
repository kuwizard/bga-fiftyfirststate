/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * fiftyfirststate.js
 *
 * 51 State user interface script
 *
 */
var isDebug = window.location.host === 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {
};

define([
    'dojo',
    'dojo/_base/declare',
    'ebg/counter',
    g_gamethemeurl + 'modules/js/Core/game.js',
    g_gamethemeurl + 'modules/js/States/DiscardCardsGameStartState.js',
    g_gamethemeurl + 'modules/js/States/PhaseOneLookoutChooseState.js',
    g_gamethemeurl + 'modules/js/States/PhaseThreeActionState.js',
    g_gamethemeurl + 'modules/js/States/FactionActions.js',
    g_gamethemeurl + 'modules/js/States/ChooseResourceSourceState.js',
    g_gamethemeurl + 'modules/js/States/ChooseResourceToStoreState.js',
    g_gamethemeurl + 'modules/js/States/DeployState.js',
    g_gamethemeurl + 'modules/js/States/ChoosePlayerToStealState.js',
    g_gamethemeurl + 'modules/js/States/ChooseDealToLoseState.js',
    g_gamethemeurl + 'modules/js/States/PhaseFourCleanupState.js',
    g_gamethemeurl + 'modules/js/States/ActivateSecondTimeState.js',
    g_gamethemeurl + 'modules/js/playerboard.js',
    g_gamethemeurl + 'modules/js/factionboard.js',
    g_gamethemeurl + 'modules/js/hand.js',
    g_gamethemeurl + 'modules/js/common.js',
    g_gamethemeurl + 'modules/js/lexemes.js',
], function (dojo, declare) {
    return declare(
        'bgagame.fiftyfirststate',
        [
            customgame.game,
            state.discardCardsGameStart,
            state.phaseOneLookoutChoose,
            state.phaseThreeAction,
            state.playerboard,
            state.factionboard,
            state.hand,
            state.common,
            state.factionActions,
            state.chooseResourceSource,
            state.chooseResourceToStore,
            state.deploy,
            state.choosePlayerToSteal,
            state.chooseDealToLose,
            state.phaseFourCleanup,
            state.activateSecondTime,
            state.lexemes,
        ],
        {
            constructor() {
                // TODO: Fix for mobile
                // this.default_viewport = 'width=600';
            },

            setup(gamedatas) {
                debug('SETUP', gamedatas);
                this.inherited(arguments);
            },

            notif_template(n) {
                debug('Notif: template', n);
            },
        }
    );
});
