<?php

namespace STATE\Managers;

use STATE\Helpers\Pieces;
use STATE\Models\Location;
use STATE\Models\Player;

class Locations extends Pieces
{
    protected static $table = 'locations';
    protected static $primary = 'location_id';
    protected static $prefix = 'location_';
    protected static $customFields = ['type'];

    /**
     * @param Player $player
     * @param int $amount
     * @return void
     */
    public static function draw($player, $amount = 1)
    {
        $pId = $player->getId();
        self::pickForLocation($amount, LOCATION_DECK, [LOCATION_HAND, $pId]);
//        Notifications::newCardsDrawn($player);
    }

    public static function discard($cardIds)
    {
        foreach ($cardIds as $cardId) {
            self::insertOnTop($cardId, LOCATION_DISCARD);
        }
    }

    protected static function cast($row)
    {
        return new Location($row);
    }

    private static $allCardTypes = [
        'AbandonedSuburbs',
        'Archive',
        'Arena',
        'Assassin',
        'AssemblyPlant',
        'Bioweaponry',
        'BoilerRoom',
        'BrickStorage',
        'BrickSupplier',
        'Camp',
        'Church',
        'CityGuards',
        'ClayPit',
        'Confessor',
        'ConstructionVehicles',
        'Convoy',
        'CornerShop',
        'Crossroads',
        'DesertedColony',
        'Docks',
        'Excavator',
        'Factory',
        'FuelTank',
        'GasolineCultist',
        'GasolineDrinkersDen',
        'GunShop',
        'Gunsmith',
        'Hideout',
        'HugeMachinery',
        'MercOutpost',
        'MethaneStorage',
        'Motel',
        'MurderersPub',
        'Museum',
        'Negotiator',
        'OilmenFortress',
        'OilRig',
        'OilTrader',
        'OldCinema',
        'ParkingLot',
        'Pub',
        'Quarry',
        'RadioactiveFuel',
        'Refinery',
        'Rubble',
        'RubbleTrader',
        'RuinedLibrary',
        'School',
        'ScrapMetal',
        'ScrapTrader',
        'Shadow',
        'Sharrash',
        'Shelter',
        'Shipwreck',
        'Skyscraper',
        'ThievesCaravan',
        'ThievesDen',
        'UndergroundWarehouse',
        'WeaponTrader',
        'WreckedTank',
    ];

    public static function setupNewGame()
    {
        foreach (self::$allCardTypes as $class) {
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

    public static function getAll()
    {
        return self::DB()->get();
    }

    public static function get($id, $raiseExceptionIfNotEnough = true)
    {
        return self::DB()
            ->where($id)
            ->getSingle();
    }
}
