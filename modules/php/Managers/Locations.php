<?php

namespace STATE\Managers;

use STATE\Core\Notifications;
use STATE\Helpers\Collection;
use STATE\Helpers\GameOptions;
use STATE\Helpers\Pieces;
use STATE\Helpers\ResourcesHelper;
use STATE\Models\Action;
use STATE\Models\Feature;
use STATE\Models\FeatureStorage;
use STATE\Models\Location;
use STATE\Models\Player;
use STATE\Models\Production;

class Locations extends Pieces
{
    protected static $table = 'locations';
    protected static $primary = 'location_id';
    protected static $prefix = 'location_';
    protected static $customFields = ['type', 'activated_times', 'is_ruined'];
    protected static $autoreshuffle = true;
    protected static $autoreshuffleCustom = [LOCATION_DECK => LOCATION_DISCARD];
    protected static $autoreshuffleListener = [
        'obj' => 'STATE\Managers\Locations',
        'method' => 'reshuffle',
    ];

    protected static function cast($row)
    {
        return self::getByType($row);
    }

    public static function draw(Player $player, int $amount = 1): Collection
    {
        $pId = $player->getId();
        return self::pickForLocation($amount, LOCATION_DECK, [LOCATION_HAND, $pId]);
    }

    public static function discard(array $cardIds): void
    {
        foreach ($cardIds as $cardId) {
            self::insertOnTop($cardId, LOCATION_DISCARD);
        }
    }

    private static $baseCardTypes = [
        CARD_ABANDONED_SUBURBS => 'AbandonedSuburbs',
        CARD_ARCHIVE => 'Archive',
        CARD_ARENA => 'Arena',
        CARD_ASSASSIN => 'Assassin',
        CARD_ASSEMBLY_PLANT => 'AssemblyPlant',
        CARD_BIOWEAPONRY => 'Bioweaponry',
        CARD_BOILER_ROOM => 'BoilerRoom',
        CARD_BRICK_STORAGE => 'BrickStorage',
        CARD_BRICK_SUPPLIER => 'BrickSupplier',
        CARD_CAMP => 'Camp',
        CARD_CHURCH => 'Church',
        CARD_CITY_GUARDS => 'CityGuards',
        CARD_CLAY_PIT => 'ClayPit',
        CARD_CONFESSOR => 'Confessor',
        CARD_CONSTRUCTION_VEHICLES => 'ConstructionVehicles',
        CARD_CONVOY => 'Convoy',
        CARD_CORNER_SHOP => 'CornerShop',
        CARD_CROSSROADS => 'Crossroads',
        CARD_DESERTED_COLONY => 'DesertedColony',
        CARD_DOCKS => 'Docks',
        CARD_EXCAVATOR => 'Excavator',
        CARD_FACTORY => 'Factory',
        CARD_FUEL_TANK => 'FuelTank',
        CARD_GASOLINE_CULTIST => 'GasolineCultist',
        CARD_GASOLINE_DEN => 'GasolineDrinkersDen',
        CARD_GUN_SHOP => 'GunShop',
        CARD_GUNSMITH => 'Gunsmith',
        CARD_HIDEOUT => 'Hideout',
        CARD_HUGE_MACHINERY => 'HugeMachinery',
        CARD_MERC_OUTPOST => 'MercOutpost',
        CARD_METHANE_STORAGE => 'MethaneStorage',
        CARD_MOTEL => 'Motel',
        CARD_MURDERERS_PUB => 'MurderersPub',
        CARD_MUSEUM => 'Museum',
        CARD_NEGOTIATOR => 'Negotiator',
        CARD_OILMEN_FORTRESS => 'OilmenFortress',
        CARD_OIL_RIG => 'OilRig',
        CARD_OIL_TRADER => 'OilTrader',
        CARD_OLD_CINEMA => 'OldCinema',
        CARD_PARKING_LOT => 'ParkingLot',
        CARD_PUB => 'Pub',
        CARD_QUARRY => 'Quarry',
        CARD_RADIOACTIVE_FUEL => 'RadioactiveFuel',
        CARD_REFINERY => 'Refinery',
        CARD_RUBBLE => 'Rubble',
        CARD_RUBBLE_TRADER => 'RubbleTrader',
        CARD_RUINED_LIBRARY => 'RuinedLibrary',
        CARD_SCHOOL => 'School',
        CARD_SCRAP_METAL => 'ScrapMetal',
        CARD_SCRAP_TRADER => 'ScrapTrader',
        CARD_SHADOW => 'Shadow',
        CARD_SHARRASH => 'Sharrash',
        CARD_SHELTER => 'Shelter',
        CARD_SHIPWRECK => 'Shipwreck',
        CARD_SKYSCRAPER => 'Skyscraper',
        CARD_THIEVES_CARAVAN => 'ThievesCaravan',
        CARD_THIEVES_DEN => 'ThievesDen',
        CARD_UNDERGROUND_WAREHOUSE => 'UndergroundWarehouse',
        CARD_WEAPON_TRADER => 'WeaponTrader',
        CARD_WRECKED_TANK => 'WreckedTank',
    ];

    private static $eraCardTypes = [
        CARD_BLACK_MARKET_CONTACTS => 'BlackMarketContacts',
        CARD_BRICK_VILLAGE => 'BrickVillage',
        CARD_BUILDERS => 'Builders',
        CARD_BUS_STATION => 'BusStation',
        CARD_CAR_GARAGE => 'CarGarage',
        CARD_COMBAT_ZONE => 'CombatZone',
        CARD_COURTHOUSE => 'Courthouse',
        CARD_DISASSEMBLY_WORKSHOP => 'DisassemblyWorkshop',
        CARD_ESPIONAGE_CENTER => 'EspionageCenter',
        CARD_EXPEDITION_CAMP => 'ExpeditionCamp',
        CARD_FOUNDATION => 'Foundation',
        CARD_GANGERS_DIVE => 'GangersDive',
        CARD_GASOLINE_TOWER => 'GasolineTower',
        CARD_GUILDS_GARAGE => 'GuildsGarage',
        CARD_HANGAR => 'Hangar',
        CARD_HAVEN => 'Haven',
        CARD_HIDDEN_FORGE => 'HiddenForge',
        CARD_HUMAN_TRAFFICER => 'HumanTrafficer',
        CARD_HUNTERS => 'Hunters',
        CARD_LABOR_CAMP => 'LaborCamp',
        CARD_LEMMYS_STORAGE => 'LemmysStorage',
        CARD_MESMERIZERS_DWELLING => 'MesmerizersDwelling',
        CARD_NATURAL_SHELTERS => 'NaturalShelters',
        CARD_OHIO_CAVALRY => 'OhioCavalry',
        CARD_OILFIELD => 'Oilfield',
        CARD_OLD_SETTLEMENTS => 'OldSettlements',
        CARD_PETES_OFFICE => 'PetesOffice',
        CARD_PICKERS => 'Pickers',
        CARD_POST_OFFICE => 'PostOffice',
        CARD_PREACHER_OF_THE_NEW_ERA => 'PreacherOfTheNewEra',
        CARD_PRODUCTION_MANAGER => 'ProductionManager',
        CARD_RADIOACTIVE_COLONY => 'RadioactiveColony',
        CARD_REHABILITATION_CENTER => 'RehabilitationCenter',
        CARD_RICKY_THE_MERCHANT => 'RickyTheMerchant',
        CARD_RIFLE => 'Rifle',
        CARD_SECRET_OUTPOST => 'SecretOutpost',
        CARD_THE_BRONX_GANG => 'TheBronxGang',
        CARD_THE_IRON_GANG => 'TheIronGang',
        CARD_TRAINING_CAMP => 'TrainingCamp',
        CARD_TRUCK => 'Truck',
    ];

    public static function setupNewGame()
    {
        $expansion = GameOptions::getExpansion();
        $baseCards = self::getCards(BASE_GAME, $expansion);
        $expansionCards = $expansion === BASE_GAME ? [] : self::getCards($expansion);
        $allCards = array_merge($baseCards, $expansionCards);
        shuffle($allCards);
        $statedCards = [];
        for ($i = count($allCards); $i > 0; $i--) {
            $statedCards[] = array_merge(array_shift($allCards), ['state' => $i]);
        }
        self::create($statedCards, LOCATION_DECK);
    }

    private static function getCards(int $expansionOrBase, int $alsoGetFromExpansion = null): array
    {
        $cards = [];
        foreach (self::getLocationsBlock($expansionOrBase) as $class) {
            $name = self::getFolder($expansionOrBase) . $class;
            /** @var Location $card */
            $card = new $name();

            for ($i = 0; $i < $card->getCopies(); $i++) {
                $cards[] = [
                    'type' => $card->getType(),
                ];
            }
            if (!is_null($alsoGetFromExpansion) && $alsoGetFromExpansion !== BASE_GAME) {
                for ($i = 0; $i < $card->getExpansionCopies()[$alsoGetFromExpansion]; $i++) {
                    $cards[] = [
                        'type' => $card->getType(),
                    ];
                }
            }
        }
        return $cards;
    }

    /**
     * @param array $params
     * @return Location
     */
    private static function getByType($params)
    {
        $type = $params['type'];
        $expansion = self::getExpansion($type);
        $name = self::getFolder($expansion) . self::getLocationsBlock($expansion)[$type];
        return new $name($params);
    }

    private static function getFolder(int $expansion): string
    {
        return 'STATE\Data\Locations\\' . [
                BASE_GAME => '',
                NEW_ERA => 'NewEra\\',
            ][$expansion];
    }

    public static function getAll()
    {
        return self::DB()->get();
    }

    /**
     * @param int $id
     * @param boolean $raiseExceptionIfNotEnough
     * @return Location
     */
    public static function get($id, $raiseExceptionIfNotEnough = true)
    {
        return self::DB()
            ->where($id)
            ->getSingle();
    }

    /**
     * @param int $pId
     * @return Collection
     */
    public static function getBoard(int $pId)
    {
        return self::getInLocation([LOCATION_BOARD, $pId]);
    }

    public static function getBoardUI(int $pId)
    {
        $board = self::getBoard($pId);
        $production = $board->filter(function ($location) {
            return $location instanceof Production;
        })->toArray();
        $feature = $board->filter(function ($location) {
            return $location instanceof Feature;
        })->toArray();
        $actions = $board->filter(function ($location) {
            return $location instanceof Action;
        })->toArray();
        return ['production' => $production, 'feature' => $feature, 'actions' => $actions];
    }

    public static function getHand(int $id): Collection
    {
        return self::getInLocation([LOCATION_HAND, $id])->sort(fn($a, $b) => strcmp($a->getDistance(), $b->getDistance()));
    }

    public static function getSprite(string $type): int
    {
        $expansion = self::getExpansion($type);
        return array_search($type, array_keys(self::getLocationsBlock($expansion)));
    }

    private static function getLocationsBlock(int $expansion): array
    {
        return [
            BASE_GAME => self::$baseCardTypes,
            NEW_ERA => self::$eraCardTypes,
        ][$expansion];
    }

    public static function getExpansion(string $type): int
    {
        $expansion = BASE_GAME;
        if (!in_array($type, array_keys(self::$baseCardTypes))) {
            if (in_array($type, array_keys(self::$eraCardTypes))) {
                $expansion = NEW_ERA;
            } else {
                throw new \BgaVisibleSystemException("getSprite: Unknown location type $type");
            }
        }
        return $expansion;
    }

    /**
     * @param int $pId
     * @return array
     */
    public static function getDealsResources(int $pId)
    {
        $resources = [];
        $locationsInDeals = self::getInLocation([LOCATION_DEALS, $pId]);
        foreach ($locationsInDeals as $location) {
            foreach (array_count_values($location->getDeals()) as $resource => $amount) {
                $resourceName = ResourcesHelper::getResourceName($resource);
                $resources[$resourceName] = isset($resources[$resourceName]) ? $resources[$resourceName] + $amount : $amount;
            }
        }
        return $resources;
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public static function resetActivatedTimes($ids)
    {
        self::DB()
            ->update(['activated_times' => 0])
            ->whereIn('location_id', $ids)
            ->run();
    }

    public static function increaseActivatedTimes(int $id, int $newAmount)
    {
        self::DB()
            ->update(['activated_times' => $newAmount])
            ->where('location_id', $id)
            ->run();
    }

    public static function ruin(Location $location): void
    {
        if ($location instanceof FeatureStorage && $location->getResourcesAmount() > 0) {
            $owner = Players::getOwner($location->getId());
            $resourcesChanged = ResourcesHelper::increaseResourcesAfterAction($owner, $location->getResources());
            Resources::deleteAll($location->getId());
            Notifications::resourcesChanged($owner, $owner->getResourcesWithNames($resourcesChanged));
        }
        self::DB()
            ->update(['is_ruined' => 1])
            ->where('location_id', $location->getId())
            ->run();
    }

    public static function unruin(Location $location): void
    {
        self::DB()
            ->update(['is_ruined' => 0])
            ->where('location_id', $location->getId())
            ->run();
    }

    public static function discardByDeal(int $resource, int $playerId)
    {
        $locationsInDeals = self::getInLocation([LOCATION_DEALS, $playerId]);
        $firstWithDeal = $locationsInDeals->filter(function ($location) use ($resource) {
            /** @var Location $location */
            return $location->getDeals()[0] === $resource;
        })->first();
        /** @var Location $firstWithDeal */
        self::discard([$firstWithDeal->getId()]);
        return $firstWithDeal;
    }

    public static function reshuffle()
    {
        Notifications::locationsReshuffle();
    }
}
