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
        'transitions' => ['' => ST_DAY_SETUP],
    ],

    ST_DAY_SETUP => [
        'name' => 'daySetup',
        'description' => clienttranslate('Waiting for ${actplayer}'),
        'descriptionmyturn' => clienttranslate('${you} must decide'),
        'type' => 'activeplayer',
        'possibleactions' => ['actExcludeClues'],
        'transitions' => [ST_END_GAME => ST_END_GAME],
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

