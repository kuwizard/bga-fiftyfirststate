<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * 51 State game states description
 *
 */

$machinestates = [
    // The initial state. Please do not modify.
    ST_GAME_SETUP => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => ['' => ST_DISCARD_CARDS_GAME_START],
    ],

    ST_DISCARD_CARDS_GAME_START => [
        'name' => 'discardCardsGameStart',
        'description' => clienttranslate('Other players must choose 2 cards to discard'),
        'descriptionmyturn' => clienttranslate('${you} must choose 2 cards to discard'),
        'action' => 'stMakeEveryoneActive',
        'type' => 'multipleactiveplayer',
        'possibleactions' => ['actDiscardCardsGameStart'],
        'transitions' => ['' => ST_NEXT_ROUND],
    ],

    ST_RESOLVE_STACK => [
        'name' => 'resolveStack',
        'type' => 'game',
        'transitions' => [],
    ],

    98 => [
        'name' => 'debug',
        'description' => clienttranslate('Waiting for ${actplayer}'),
        'descriptionmyturn' => clienttranslate('${you} must decide'),
        'type' => 'activeplayer',
        'possibleactions' => ['actTest'],
    ],

    ST_NEXT_ROUND => [
        'name' => 'nextRound',
        'description' => '',
        'action' => 'stNextRound',
        'type' => 'game',
    ],

    ST_PHASE_ONE_LOOKOUT_SETUP => [
        'name' => 'phaseOneLookoutSetup',
        'description' => '',
        'action' => 'stPhaseOneLookoutSetup',
        'type' => 'game',
    ],

    ST_PHASE_ONE_LOOKOUT_CHOOSE => [
        'name' => 'phaseOneLookoutChoose',
        'description' => clienttranslate('${actplayer} must choose a card'),
        'descriptionmyturn' => clienttranslate('${you} must choose a card'),
        'args' => 'argPhaseOneLookoutChoose',
        'type' => 'activeplayer',
        'updateGameProgression' => true,
        'possibleactions' => ['actChooseCardLookout'],
    ],

    ST_PHASE_ONE_LOOKOUT_DRAW => [
        'name' => 'phaseOneLookoutDraw',
        'description' => '',
        'action' => 'stPhaseOneLookoutDraw',
        'type' => 'game',
    ],

    ST_PHASE_ONE_LOOKOUT_DISCARD => [
        'name' => 'phaseOneLookoutDiscard',
        'description' => '',
        'action' => 'stPhaseOneLookoutDiscard',
        'type' => 'game',
    ],

    ST_PHASE_TWO_PRODUCTION => [
        'name' => 'phaseTwoProduction',
        'description' => '',
        'action' => 'stPhaseTwoProduction',
        'type' => 'game',
    ],

    ST_PHASE_THREE_ACTION => [
        'name' => 'phaseThreeAction',
        'description' => clienttranslate('${actplayer} must choose an action or pass'),
        'descriptionmyturn' => clienttranslate('${you} must choose an action or pass'),
//        'args' => 'argPhaseThreeAction',
        'type' => 'activeplayer',
        'updateGameProgression' => true,
        'possibleactions' => ['actActionPass', 'actDoSomething'],
    ],

    ST_PHASE_FOUR_CLEANUP => [
        'name' => 'phaseFourCleanup',
        'description' => '',
        'action' => 'stPhaseFourCleanup',
        'type' => 'game',
    ],

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    ST_END_GAME => [
        'name' => 'gameEnd',
        'description' => clienttranslate('End of game'),
        'type' => 'manager',
        'action' => 'stGameEnd',
        'args' => 'argGameEnd',
    ],
];

