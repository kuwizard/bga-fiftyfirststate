<?php

/*
 * State constants
 */
define('ST_GAME_SETUP', 1);
define('ST_CHOOSE_FACTION', 4);
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
define('ST_CREATE_RESOURCE_SOURCE_MAP', 35);
define('ST_PROCESS_SOURCE_MAP', 36);
define('ST_CHOOSE_RESOURCE_SOURCE', 37);
define('ST_CHOOSE_RESOURCE_TO_STORE', 38);
define('ST_DEVELOP_CHOOSE_FROM_HAND', 39);
define('ST_DEVELOP_CHOOSE_DESTINATION', 40);
define('ST_OPEN_PRODUCTION_OR_RAZE', 41);
define('ST_CHOOSE_PLAYER_TO_STEAL', 42);
define('ST_CHOOSE_RESOURCE_TO_STEAL', 43);
define('ST_CHOOSE_DEAL_TO_LOSE', 44);
define('ST_ACTIVATE_SECOND_TIME', 45);
define('ST_CHOOSE_RESOURCE_TO_SPEND', 46);
define('ST_ACTIVATE_SPEND_WORKERS_AGAIN', 47);
define('ST_ACTIVATE_PRODUCTION', 48);
define('ST_PHASE_FOUR_CLEANUP', 95);
define('ST_CONFIRM_TURN_END', 96);

define('ST_END_GAME', 99);
define('ST_RESOLVE_STACK', 100);

/*
 * Factions
 */
define('FACTION_NEW_YORK', 500);
define('FACTION_APPALACHIAN', 510);
define('FACTION_MUTANTS', 520);
define('FACTION_MERCHANTS', 530);

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
define('LOCATION_CONNECTIONS_BLUE_DISCARD', 'blue_discard');
define('LOCATION_CONNECTIONS_RED_DISCARD', 'red_discard');
define('LOCATION_LOOKOUT', 'lookout');

/*
 * Locations types
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

// New Era locations
define('CARD_BLACK_MARKET_CONTACTS', 'black_market_contacts');
define('CARD_BRICK_VILLAGE', 'brick_village');
define('CARD_BUILDERS', 'builders');
define('CARD_BUS_STATION', 'bus_station');
define('CARD_CAR_GARAGE', 'car_garage');
define('CARD_COMBAT_ZONE', 'combat_zone');
define('CARD_COURTHOUSE', 'courthouse');
define('CARD_DISASSEMBLY_WORKSHOP', 'disassembly_workshop');
define('CARD_ESPIONAGE_CENTER', 'espionage_center');
define('CARD_EXPEDITION_CAMP', 'expedition_camp');
define('CARD_FOUNDATION', 'foundation');
define('CARD_GANGERS_DIVE', 'gangers_dive');
define('CARD_GASOLINE_TOWER', 'gasoline_tower');
define('CARD_GUILDS_GARAGE', 'guilds_garage');
define('CARD_HANGAR', 'hangar');
define('CARD_HAVEN', 'haven');
define('CARD_HIDDEN_FORGE', 'hidden_forge');
define('CARD_HUMAN_TRAFFICER', 'human_trafficer');
define('CARD_HUNTERS', 'hunters');
define('CARD_LABOR_CAMP', 'labor_camp');
define('CARD_LEMMYS_STORAGE', 'lemmys_storage');
define('CARD_MESMERIZERS_DWELLING', 'mesmerizers_dwelling');
define('CARD_NATURAL_SHELTERS', 'natural_shelters');
define('CARD_OHIO_CAVALRY', 'ohio_cavalry');
define('CARD_OILFIELD', 'oilfield');
define('CARD_OLD_SETTLEMENTS', 'old_settlements');
define('CARD_PETES_OFFICE', 'petes_office');
define('CARD_PICKERS', 'pickers');
define('CARD_POST_OFFICE', 'post_office');
define('CARD_PREACHER_OF_THE_NEW_ERA', 'preacher_of_the_new_era');
define('CARD_PRODUCTION_MANAGER', 'production_manager');
define('CARD_RADIOACTIVE_COLONY', 'radioactive_colony');
define('CARD_REHABILITATION_CENTER', 'rehabilitation_center');
define('CARD_RICKY_THE_MERCHANT', 'ricky_the_merchant');
define('CARD_RIFLE', 'rifle');
define('CARD_SECRET_OUTPOST', 'secret_outpost');
define('CARD_THE_BRONX_GANG', 'the_bronx_gang');
define('CARD_THE_IRON_GANG', 'the_iron_gang');
define('CARD_TRAINING_CAMP', 'training_camp');
define('CARD_TRUCK', 'truck');

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
define('RESOURCE_FUEL', 4400);
define('RESOURCE_GUN', 4401);
define('RESOURCE_IRON', 4402);
define('RESOURCE_BRICK', 4403);
define('RESOURCE_WORKER', 4404);
define('RESOURCE_ARROW_GREY', 4405);
define('RESOURCE_ARROW_RED', 4406);
define('RESOURCE_ARROW_BLUE', 4407);
define('RESOURCE_ARROW_UNIVERSAL', 4408);
define('RESOURCE_AMMO', 4409);
define('RESOURCE_DEFENCE', 4410);
define('RESOURCE_DEVELOPMENT', 4411);
define('RESOURCE_CARD', 4412);
define('RESOURCE_VP', 4413);
define('RESOURCE_DEAL', 4414);
define('RESOURCE_ANY_OF_MAIN', 4415);
define('RESOURCE_ANY_OF_MAIN_PLUS_CARD', 4416);

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
define('ICON_RESPIRATOR', 10);

/*
 * Action types
 */
define('ACTION_TYPE_SPEND', 0);
define('ACTION_TYPE_STEAL_ANOTHER_PLAYER', 1);
define('ACTION_TYPE_ACTIVATE_PRODUCTION', 2);

/*
 * Location action types
 */
define('LOCATION_ACTION_RAZE', 'raze');
define('LOCATION_ACTION_DEAL', 'deal');
define('LOCATION_ACTION_BUILD', 'build');
define('LOCATION_ACTION_RAZE_OTHER', 'razeOtherPlayer');
define('LOCATION_ACTION_DEVELOP', 'develop');

/*
 * Texts types
 */
define('TEXT_TYPE', 'type');
define('TEXT_DESCRIPTION', 'description');
define('TEXT_BUILDING_BONUS', 'bbonus');
define('TEXT_BONUS_DESCRIPTION', 'bonusDescription');
define('TEXT_MAY_BE_ACTIVATED_TWICE', 'mayBeActivated');

/*
 * Player preferences
 */
define('NEW_YORK_PREFERENCE', 201);
define('APPALACHIAN_PREFERENCE', 202);
define('MUTANTS_PREFERENCE', 203);
define('MERCHANTS_PREFERENCE', 204);

define('NEW_YORK_SIDE', 301);
define('APPALACHIAN_SIDE', 302);
define('MUTANTS_SIDE', 303);
define('MERCHANTS_SIDE', 304);

/*
 * Game Options
 */
define('OPT_EXPANSION', 101);
define('BASE_GAME', 1011);
define('NEW_ERA', 1012);

/*
 * Global constants
 */
define('GLOBAL_START_CARDS', 6);
define('GLOBAL_END_OF_GAME_VP', 25);

const ALL_RESOURCES_LIST = [
    RESOURCE_FUEL,
    RESOURCE_GUN,
    RESOURCE_IRON,
    RESOURCE_BRICK,
    RESOURCE_WORKER,
    RESOURCE_ARROW_GREY,
    RESOURCE_ARROW_RED,
    RESOURCE_ARROW_BLUE,
    RESOURCE_ARROW_UNIVERSAL,
    RESOURCE_AMMO,
    RESOURCE_DEFENCE,
    RESOURCE_DEVELOPMENT,
];

const MAIN_RESOURCES_LIST = [
    RESOURCE_FUEL,
    RESOURCE_GUN,
    RESOURCE_IRON,
    RESOURCE_BRICK,
    RESOURCE_AMMO,
];