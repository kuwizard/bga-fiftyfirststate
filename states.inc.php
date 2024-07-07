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
        'description' => clienttranslate('${actplayer} must choose 2 cards to discard'),
        'descriptionmyturn' => clienttranslate('${you} must choose 2 cards to discard'),
        'action' => 'stMakeEveryoneActive',
        'type' => 'multipleactiveplayer',
        'possibleactions' => ['actDiscardCardsGameStart'],
        'transitions' => ['' => 98],
    ],

    98 => [
        'name' => 'debug',
        'description' => clienttranslate('Waiting for ${actplayer}'),
        'descriptionmyturn' => clienttranslate('${you} must decide'),
        'type' => 'activeplayer',
        'possibleactions' => ['actDiscardCardsGameStart'],
        'transitions' => ['' => ST_NEXT_TURN],
    ],

    ST_NEXT_TURN => [
        'name' => 'nextTurn',
        'description' => '',
        'action' => 'stNextTurn',
        'type' => 'game',
    ],

    ST_TURN_PHASE_ONE_LOOKOUT => [
        'name' => 'turnPhaseOneLookout',
        'description' => clienttranslate('${actplayer} must choose a card '),
        'descriptionmyturn' => clienttranslate('${you} should place a poker card in any open slot'),
        'args' => 'argStepOnePlan',
        'type' => 'activeplayer',
        'updateGameProgression' => true,
        'possibleactions' => ['actPlaceCard', 'actUndo'],
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

