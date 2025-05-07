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

require_once "modules/php/constants.inc.php";

$machinestates = [
    // The initial state. Please do not modify.
    ST_GAME_SETUP => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => ['' => ST_CHOOSE_FACTION],
    ],

    ST_CHOOSE_FACTION => [
        'name' => 'chooseFaction',
        'description' => clienttranslate('Other players must set their faction preferences'),
        'descriptionmyturn' => clienttranslate('${you} must set your faction preferences'),
        'action' => 'stMakeEveryoneActive',
        'args' => 'argChooseFaction',
        'type' => 'multipleactiveplayer',
        'possibleactions' => ['actChooseFactionsPreferences', 'actIDontCare', 'actChangedMind'],
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
        'args' => 'argNoNotify',
        'transitions' => [],
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
        'description' => clienttranslate('${actplayer} must choose a Location card'),
        'descriptionmyturn' => clienttranslate('${you} must choose a Location card'),
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
        'description' => clienttranslate('${actplayer} must choose an action or a Location/Connection card'),
        'descriptionmyturn' => clienttranslate('${you} must choose an action or a Location/Connection card'),
        'args' => 'argPhaseThreeAction',
        'type' => 'activeplayer',
        'updateGameProgression' => true,
        'possibleactions' => [
            'actActionPass',
            'actUseLocation',
            'actActivateLocation',
            'actUseOtherPlayerLocation',
            'actUseOpenProduction',
            'actDevelop',
            'actTakeConnection',
            'actPlayConnection',
            'actEnablePlaceDefenceState',
            // Actions leading to another sub-phase with a choice
            'actEnableFactionActions',
            'actSpendWorkers',
        ],
    ],

    ST_SPEND_WORKERS => [
        'name' => 'spendWorkers',
        'description' => clienttranslate('${actplayer} must choose a resource to gain'),
        'descriptionmyturn' => clienttranslate('${you} must choose a resource to gain'),
        'type' => 'activeplayer',
        'possibleactions' => ['actGainResourceForWorkers', 'actUndo'],
    ],

    ST_FACTION_ACTIONS => [
        'name' => 'factionActions',
        'description' => clienttranslate('${actplayer} must choose a Faction action to activate'),
        'descriptionmyturn' => clienttranslate('${you} must choose a Faction action to activate'),
        'args' => 'argFactionActions',
        'type' => 'activeplayer',
        'possibleactions' => ['actFactionAct', 'actSpendWorkers', 'actUndo'],
    ],

    ST_LOCATION_ACTIONS => [
        'name' => 'locationActions',
        'description' => clienttranslate('${actplayer} must choose what to do with a location'),
        'descriptionmyturn' => clienttranslate('${you} must choose what to do with the chosen location'),
        'args' => 'argLocationActions',
        'type' => 'activeplayer',
        'possibleactions' => ['actLocationRaze', 'actLocationDeal', 'actLocationBuild', 'actUndo'],
    ],

    ST_DISCARD_LOCATION_FOR_RESOURCES => [
        'name' => 'discardLocationForResources',
        'description' => clienttranslate('${actplayer} must choose a card from hand to discard'),
        'descriptionmyturn' => clienttranslate('${you} must choose a card from hand to discard'),
        'type' => 'activeplayer',
        'possibleactions' => ['actDiscardLocation', 'actDiscardConnection'],
    ],

    ST_CREATE_RESOURCE_SOURCE_MAP => [
        'name' => 'createResourceSourceMap',
        'description' => '',
        'args' => 'argNoNotify',
        'action' => 'stCreateResourceSourceMap',
        'type' => 'game',
    ],

    ST_PROCESS_SOURCE_MAP => [
        'name' => 'processSourceMap',
        'description' => '',
        'args' => 'argNoNotify',
        'action' => 'stProcessSourceMap',
        'type' => 'game',
    ],

    ST_CHOOSE_RESOURCE_SOURCE => [
        'name' => 'chooseResourceSource',
        'description' => clienttranslate('${actplayer} must choose resources to spend'),
        'descriptionmyturn' => clienttranslate('${you} need to spend ${resourcesList}. ${spendText}'),
        'args' => 'argChooseResourceSource',
        'type' => 'activeplayer',
        'possibleactions' => ['actChooseSource'],
    ],

    ST_CHOOSE_RESOURCE_TO_STORE => [
        'name' => 'chooseResourceToStore',
        'description' => clienttranslate('Other players must choose a resource to store'),
        'descriptionmyturn' => clienttranslate('${you} must choose a resource to store'),
        'args' => 'argChooseResourceToStore',
        'type' => 'activeplayer',
        'possibleactions' => ['actChooseResourceToStore', 'actPassStoringResource', 'actResetTurn'],
    ],

    ST_DEVELOP_CHOOSE_FROM_HAND => [
        'name' => 'developChooseFromHand',
        'description' => clienttranslate('${actplayer} must choose which card to develop'),
        'descriptionmyturn' => clienttranslate('${you} must choose which card to develop'),
        'args' => 'argDevelopChooseFromHand',
        'type' => 'activeplayer',
        'possibleactions' => ['actDevelopChooseFromHand', 'actUndo'],
    ],

    ST_DEVELOP_CHOOSE_DESTINATION => [
        'name' => 'developChooseDestination',
        'description' => clienttranslate('${actplayer} must choose which card to replace'),
        'descriptionmyturn' => clienttranslate('${you} must choose which card to replace'),
        'args' => 'argDevelopChooseDestination',
        'type' => 'activeplayer',
        'possibleactions' => ['actDevelopChooseDestination', 'actUndo'],
    ],

    ST_OPEN_PRODUCTION_OR_RAZE => [
        'name' => 'openProductionOrRaze',
        'description' => clienttranslate('${actplayer} must choose what to do with the chosen location'),
        'descriptionmyturn' => clienttranslate('${you} must choose what to do with the chosen location'),
        'args' => 'argOpenProductionOrRaze',
        'type' => 'activeplayer',
        'possibleactions' => ['actOptionOpenProduction', 'actOptionRaze', 'actUndo'],
    ],

    ST_CHOOSE_PLAYER_TO_STEAL => [
        'name' => 'choosePlayerToSteal',
        'description' => clienttranslate('${actplayer} must choose another player and a resource to take'),
        'descriptionmyturn' => clienttranslate('${you} must choose another player and a resource to take'),
        'args' => 'argChoosePlayerToSteal',
        'type' => 'activeplayer',
        'possibleactions' => ['actChoosePlayerToSteal', 'actChoosePlayerAndResourceToSteal', 'actUndo'],
    ],

    ST_CHOOSE_RESOURCE_TO_STEAL => [
        'name' => 'chooseResourceToSteal',
        'description' => clienttranslate('${actplayer} must choose a resource to take'),
        'descriptionmyturn' => clienttranslate('${you} must choose a resource to take from ${player_name}'),
        'args' => 'argChooseResourceToSteal',
        'type' => 'activeplayer',
        'possibleactions' => ['actChooseResourceToSteal', 'actUndo'],
    ],

    ST_CHOOSE_RESOURCE_TO_SPEND => [
        'name' => 'chooseResourceToSpend',
        'description' => clienttranslate('${actplayer} must choose a resource to spend'),
        'descriptionmyturn' => clienttranslate('${you} must choose a resource to spend'),
        'args' => 'argChooseResourceToSpend',
        'type' => 'activeplayer',
        'possibleactions' => ['actChooseResourceToSpend', 'actUndo'],
    ],

    ST_CHOOSE_DEAL_TO_LOSE => [
        'name' => 'chooseDealToLose',
        'description' => clienttranslate('${actplayer} must choose a deal to lose'),
        'descriptionmyturn' => clienttranslate('${you} must choose a deal to lose'),
        'args' => 'argChooseDealToLose',
        'type' => 'activeplayer',
        'possibleactions' => ['actChooseDeal', 'actUndo'],
    ],

    ST_ACTIVATE_SECOND_TIME => [
        'name' => 'activateSecondTime',
        'description' => clienttranslate('${actplayer} may choose to activate the same location again'),
        'descriptionmyturn' => clienttranslate('Do ${you} want to activate the same location again?'),
        'args' => 'argActivateSecondTime',
        'action' => 'stActivateSecondTime',
        'type' => 'activeplayer',
        'possibleactions' => ['actActivateAgain', 'actDoNotActivateAgain'],
    ],

    ST_ACTIVATE_SPEND_WORKERS_AGAIN => [
        'name' => 'activateSpendWorkersAgain',
        'description' => clienttranslate('${actplayer} may choose to activate the same faction action (spend 2 workers) again'),
        'descriptionmyturn' => clienttranslate('Do ${you} want to activate the same faction action (spend 2 workers) again?'),
        'args' => 'argActivateSecondTime',
        'type' => 'activeplayer',
        'possibleactions' => ['actActivateAgain', 'actDoNotActivateAgain'],
    ],

    ST_ACTIVATE_PRODUCTION => [
        'name' => 'activateProduction',
        'description' => clienttranslate('${actplayer} must choose a Production to activate'),
        'descriptionmyturn' => clienttranslate('${you} must choose a Production to activate'),
        'args' => 'argActivateProduction',
        'type' => 'activeplayer',
        'possibleactions' => ['actActivateProduction', 'actUndo'],
    ],

    ST_PLACE_DEFENCE => [
        'name' => 'placeDefence',
        'description' => clienttranslate('${actplayer} must place a defence token'),
        'descriptionmyturn' => clienttranslate('${you} must place a defence token'),
        'args' => 'argPlaceDefence',
        'type' => 'activeplayer',
        'possibleactions' => ['actPlaceDefence', 'actUndo'],
    ],

    ST_PHASE_FOUR_CLEANUP => [
        'name' => 'phaseFourCleanup',
        'description' => '',
        'action' => 'stPhaseFourCleanup',
        'type' => 'game',
    ],

    ST_CONFIRM_TURN_END => [
        'name' => 'confirmTurnEnd',
        'description' => clienttranslate('${actplayer} must confirm turn end'),
        'descriptionmyturn' => clienttranslate('${you} must confirm turn end'),
        'type' => 'activeplayer',
        'possibleactions' => ['actConfirmTurnEnd', 'actResetTurn'],
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

