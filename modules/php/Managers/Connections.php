<?php

namespace STATE\Managers;

use STATE\Helpers\Collection;
use STATE\Helpers\Pieces;
use STATE\Models\Connection;

class Connections extends Pieces
{
    protected static $table = 'connections';
    protected static $primary = 'connection_id';
    protected static $prefix = 'connection_';
    protected static $customFields = ['type'];

    protected static function cast($row)
    {
        return self::getByType($row);
    }

    private static $blueCardTypes = [
        CONNECTION_JUNK_TRAIN => 'JunkTrain',
        CONNECTION_MERCHANTS => 'Merchants',
    ];

    private static $redCardTypes = [
        CONNECTION_PUNKS => 'Punks',
        CONNECTION_THUGS => 'Thugs',
    ];

    public static function setupNewGame()
    {
        foreach ([
            'blueCardTypes' => LOCATION_CONNECTIONS_BLUE_DECK,
            'redCardTypes' => LOCATION_CONNECTIONS_RED_DECK,
        ] as $deck => $location) {
            foreach (array_values(self::$$deck) as $class) {
                $name = "STATE\Data\Connections\\" . $class;
                /** @var Connection $card */
                $card = new $name();
                for ($i = 0; $i < $card->getCopies(); $i++) {
                    $cards[] = [
                        'type' => $card->getType(),
                    ];
                }
            }
            shuffle($cards);
            self::create($cards, $location);
            $cards = [];
        }
    }

    /**
     * @param array $params
     * @return Connection
     */
    private static function getByType($params)
    {
        $params['location'] = $params['location'] ?? $params['connection_location'];
        $deck = in_array($params['location'], [LOCATION_CONNECTIONS_BLUE_DECK, LOCATION_CONNECTIONS_BLUE_FLIPPED]
        ) ? self::$blueCardTypes : self::$redCardTypes;
        $name = "STATE\Data\Connections\\" . $deck[$params['type']];
        return new $name($params);
    }

    public static function getAll()
    {
        return self::DB()->get();
    }

    /**
     * @param int $id
     * @param bool $raiseExceptionIfNotEnough
     * @return Connection
     */
    public static function get($id = null, $raiseExceptionIfNotEnough = true)
    {
        return self::DB()
            ->where($id)
            ->getSingle();
    }

    /**
     * @return Collection
     */
    public static function getBothAvailable()
    {
        $available = [self::getTopOf(LOCATION_CONNECTIONS_BLUE_FLIPPED), self::getTopOf(LOCATION_CONNECTIONS_RED_FLIPPED)];
        return new Collection(array_filter($available));
    }

    public static function discardFlippedEndOfRound()
    {
        foreach ([LOCATION_CONNECTIONS_BLUE_FLIPPED, LOCATION_CONNECTIONS_RED_FLIPPED] as $location) {
            self::moveAllInLocation($location, LOCATION_DISCARD);
        }
    }

    /**
     * @param boolean $isBlue
     * @return void
     */
    public static function discard($id)
    {
        self::move($id, LOCATION_DISCARD);
    }

    public static function flipForNewRound()
    {
        foreach ([
            LOCATION_CONNECTIONS_BLUE_DECK => LOCATION_CONNECTIONS_BLUE_FLIPPED,
            LOCATION_CONNECTIONS_RED_DECK => LOCATION_CONNECTIONS_RED_FLIPPED,
        ] as $location => $flipped) {
            self::move(self::getTopOf($location)->getId(), $flipped);
        }
    }

    public static function getSprite($type): int
    {
        return [
            CONNECTION_JUNK_TRAIN => 0,
            CONNECTION_MERCHANTS => 1,
            CONNECTION_PUNKS => 2,
            CONNECTION_THUGS => 3,
        ][$type];
    }
}
