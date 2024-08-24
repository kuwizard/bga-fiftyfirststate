<?php

namespace STATE\Managers;

use STATE\Helpers\Collection;
use STATE\Helpers\Pieces;
use STATE\Helpers\Resources;
use STATE\Models\Action;
use STATE\Models\Feature;
use STATE\Models\Location;
use STATE\Models\Player;
use STATE\Models\Production;

class Locations extends Pieces
{
    protected static $table = 'locations';
    protected static $primary = 'location_id';
    protected static $prefix = 'location_';
    protected static $customFields = ['type', 'activated_times'];

    protected static function cast($row)
    {
        return self::getByType($row);
    }

    /**
     * @param Player $player
     * @param int $amount
     * @return void
     */
    public static function draw($player, $amount = 1)
    {
        $pId = $player->getId();
        self::pickForLocation($amount, LOCATION_DECK, [LOCATION_HAND, $pId]);
    }

    public static function discard($cardIds)
    {
        foreach ($cardIds as $cardId) {
            self::insertOnTop($cardId, LOCATION_DISCARD);
        }
    }

    private static $allCardTypes = [
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

    public static function setupNewGame()
    {
        foreach (array_values(self::$allCardTypes) as $class) {
            $name = "STATE\Data\Locations\\" . $class;
            /** @var Location $card */
            $card = new $name();

            for ($i = 0; $i < $card->getCopies(); $i++) {
                $cards[] = [
                    'type' => $card->getType(),
                ];
            }
        }
        shuffle($cards);
        $statedCards = [];
        for ($i = count($cards); $i > 0; $i--) {
            $statedCards[] = array_merge(array_shift($cards), ['state' => $i]);
        }
        self::create($statedCards, LOCATION_DECK);
    }

    /**
     * @param array $params
     * @return Location
     */
    private static function getByType($params)
    {
        $name = "STATE\Data\Locations\\" . self::$allCardTypes[$params['type']];
        return new $name($params);
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

    public static function getHand(int $id): array
    {
        return self::getInLocation([LOCATION_HAND, $id])->toArray();
    }

    /**
     * @param string $type
     * @return int
     */
    public static function getSprite($type)
    {
        return array_search($type, array_keys(self::$allCardTypes));
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
                $resourceName = Resources::getResourceName($resource);
                $resources[$resourceName] = isset($resources[$resourceName]) ? $resources[$resourceName] + $amount : $amount;
            }
        }
        return $resources;
    }

    public static function resetActivatedTimes()
    {
        self::DB()
            ->update(['activated_times' => 0])
            ->run();
    }
}
