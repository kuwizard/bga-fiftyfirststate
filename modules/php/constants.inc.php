<?php

/*
 * State constants
 */
define('ST_GAME_SETUP', 1);
define('ST_DISCARD_CARDS_GAME_START', 2);
define('ST_NEXT_TURN', 3);
define('ST_TURN_PHASE_ONE_LOOKOUT', 4);

define('ST_END_GAME', 99);
define('ST_RESOLVE_STACK', 100);

/*
 * Factions
 */
define('FACTION_NEW_YORK', 0);
define('FACTION_APPALACHIAN', 1);
define('FACTION_MUTANTS', 2);
define('FACTION_MERCHANTS', 3);

/*
 * Cards locations
 */
define('LOCATION_DECK', 'deck');
define('LOCATION_DISCARD', 'discard');
define('LOCATION_HAND', 'hand');
define('LOCATION_BOARD', 'board');
define('LOCATION_DEALS', 'deals');

/*
 * Cards types
 */
define('CARD_FUEL_TANK', 'fuel_tank');
define('CARD_CROSSROADS', 'crossroads');
define('CARD_OILMEN_FORTRESS', 'oilmen_fortress');

/*
 * Resources
 */
define('RESOURCE_FUEL', 0);
define('RESOURCE_GUN', 1);
define('RESOURCE_IRON', 2);
define('RESOURCE_BRICK', 3);
define('RESOURCE_WORKER', 4);
define('RESOURCE_ARROW_GREY', 5);
define('RESOURCE_ARROW_RED', 6);
define('RESOURCE_ARROW_BLUE', 7);
define('RESOURCE_ARROW_UNIVERSAL', 8);
define('RESOURCE_AMMO', 9);
define('RESOURCE_DEFENCE', 10);
define('RESOURCE_DEVELOPMENT', 11);
define('RESOURCE_CARD', 12);
define('RESOURCE_VP', 13);

/*
 * Icons
 */
define('ICON_FUEL', 0);
define('ICON_GUN', 1);
define('ICON_IRON', 2);
define('ICON_BRICK', 3);
define('ICON_CARD', 4);
define('ICON_WORKER', 5);
define('ICON_VP', 6);
define('ICON_CHURCH', 7);
define('ICON_ARROW', 8);
define('ICON_AMMO', 9);

/*
 * Features
 */
define('FEATURE_NONE', 0);

/*
 * Action types
 */
define('ACTION_TYPE_SPEND', 0);

/*
 * Global constants
 */
define('GLOBAL_START_CARDS', 6);