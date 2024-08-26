<?php

/*
 * State constants
 */
define('ST_GAME_SETUP', 1);
define('ST_DISCARD_CARDS_GAME_START', 2);
define('ST_NEXT_ROUND', 3);
define('ST_PHASE_ONE_LOOKOUT_SETUP', 10);
define('ST_PHASE_ONE_LOOKOUT_CHOOSE', 11);
define('ST_PHASE_ONE_LOOKOUT_DRAW', 12);
define('ST_PHASE_ONE_LOOKOUT_DISCARD', 13);
define('ST_PHASE_TWO_PRODUCTION', 20);
define('ST_PHASE_THREE_ACTION', 30);
define('ST_SPEND_WORKERS', 31);
define('ST_FACTION_ACTIONS', 32);
define('ST_LOCATION_ACTIONS', 33);
define('ST_DISCARD_LOCATION_FOR_RESOURCES', 34);
define('ST_PHASE_FOUR_CLEANUP', 95);

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
define('LOCATION_CONNECTIONS_BLUE_DECK', 'blue_deck');
define('LOCATION_CONNECTIONS_RED_DECK', 'red_deck');
define('LOCATION_CONNECTIONS_BLUE_FLIPPED', 'blue_flipped');
define('LOCATION_CONNECTIONS_RED_FLIPPED', 'red_flipped');
define('LOCATION_LOOKOUT', 'lookout');

/*
 * Cards types
 */
define('CARD_FUEL_TANK', 'fuel_tank');
define('CARD_CROSSROADS', 'crossroads');
define('CARD_OILMEN_FORTRESS', 'oilmen_fortress');
define('CARD_GASOLINE_DEN', 'gasoline_den');
define('CARD_CONSTRUCTION_VEHICLES', 'construction_vehicles');
define('CARD_SCHOOL', 'school');
define('CARD_CORNER_SHOP', 'corner_shop');
define('CARD_SKYSCRAPER', 'skyscraper');
define('CARD_CONVOY', 'convoy');
define('CARD_RUINED_LIBRARY', 'ruined_library');
define('CARD_GUN_SHOP', 'gun_shop');
define('CARD_SCRAP_METAL', 'scrap_metal');
define('CARD_QUARRY', 'quarry');
define('CARD_RUBBLE_TRADER', 'rubble_trader');
define('CARD_BRICK_STORAGE', 'brick_storage');
define('CARD_DESERTED_COLONY', 'deserted_colony');
define('CARD_CHURCH', 'church');
define('CARD_PUB', 'pub');
define('CARD_WRECKED_TANK', 'wrecked_tank');
define('CARD_THIEVES_CARAVAN', 'thieves_caravan');
define('CARD_BRICK_SUPPLIER', 'brick_supplier');
define('CARD_RADIOACTIVE_FUEL', 'radioactive_fuel');
define('CARD_ASSEMBLY_PLANT', 'assembly_plant');
define('CARD_OLD_CINEMA', 'old_cinema');
define('CARD_SHELTER', 'shelter');
define('CARD_FACTORY', 'factory');
define('CARD_CONFESSOR', 'confessor');
define('CARD_SHIPWRECK', 'shipwreck');
define('CARD_ASSASSIN', 'assassin');
define('CARD_SCRAP_TRADER', 'scrap_trader');
define('CARD_BIOWEAPONRY', 'bioweaponry');
define('CARD_CLAY_PIT', 'clay_pit');
define('CARD_MERC_OUTPOST', 'merc_outpost');
define('CARD_SHARRASH', 'sharrash');
define('CARD_OIL_TRADER', 'oil_trader');
define('CARD_GASOLINE_CULTIST', 'gasoline_cultist');
define('CARD_HIDEOUT', 'hideout');
define('CARD_NEGOTIATOR', 'negotiator');
define('CARD_CAMP', 'camp');
define('CARD_GUNSMITH', 'gunsmith');
define('CARD_PARKING_LOT', 'parking_lot');
define('CARD_HUGE_MACHINERY', 'huge_machinery');
define('CARD_EXCAVATOR', 'excavator');
define('CARD_DOCKS', 'docks');
define('CARD_ARENA', 'arena');
define('CARD_RUBBLE', 'rubble');
define('CARD_CITY_GUARDS', 'city_guards');
define('CARD_METHANE_STORAGE', 'methan_storage');
define('CARD_ARCHIVE', 'archive');
define('CARD_MUSEUM', 'museum');
define('CARD_MURDERERS_PUB', 'murderers_pub');
define('CARD_OIL_RIG', 'oil_rig');
define('CARD_WEAPON_TRADER', 'weapon_trader');
define('CARD_REFINERY', 'refinery');
define('CARD_MOTEL', 'motel');
define('CARD_THIEVES_DEN', 'thieves_den');
define('CARD_ABANDONED_SUBURBS', 'abandoned_suburbs');
define('CARD_SHADOW', 'shadow');
define('CARD_BOILER_ROOM', 'boiler_room');
define('CARD_UNDERGROUND_WAREHOUSE', 'underground_warehouse');


/*
 * Connections types
 */
define('CONNECTION_JUNK_TRAIN', 'junk_train');
define('CONNECTION_MERCHANTS', 'merchants');
define('CONNECTION_PUNKS', 'punks');
define('CONNECTION_THUGS', 'thugs');

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
define('FEATURE_PLACE_RESOURCES', 1);

/*
 * Action types
 */
define('ACTION_TYPE_SPEND', 0);

/*
 * Location action types
 */
define('LOCATION_ACTION_RAZE', 'raze');
define('LOCATION_ACTION_DEAL', 'deal');
define('LOCATION_ACTION_BUILD', 'build');

/*
 * Global constants
 */
define('GLOBAL_START_CARDS', 6);