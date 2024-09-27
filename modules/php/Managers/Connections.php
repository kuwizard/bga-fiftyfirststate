<?php

namespace STATE\Managers;

use STATE\Core\Notifications;
use STATE\Helpers\Collection;
use STATE\Helpers\Pieces;
use STATE\Models\Connection;

class Connections extends Pieces
{
    protected static $table = 'connections';
    protected static $primary = 'connection_id';
    protected static $prefix = 'connection_';
    protected static $customFields = ['type'];
    protected static $autoreshuffle = true;
    protected static $autoreshuffleCustom = [
        LOCATION_CONNECTIONS_BLUE_DECK => LOCATION_CONNECTIONS_BLUE_DISCARD,
        LOCATION_CONNECTIONS_RED_DECK => LOCATION_CONNECTIONS_RED_DISCARD,
    ];
    protected static $autoreshuffleListener = [
        'obj' => 'STATE\Managers\Connections',
        'method' => 'reshuffle',
    ];

    protected static function cast($row)
    {
        $deck = in_array(
            $row['type'],
            array_keys(self::$blueCardTypes)
        ) ? self::$blueCardTypes : self::$redCardTypes;
        $name = "STATE\Data\Connections\\" . $deck[$row['type']];
        return new $name($row);
    }

    public static function callReformDeckFromDiscard(string $fromLocation)
    {
        if ($fromLocation === LOCATION_CONNECTIONS_BLUE_DECK) {
            self::reformDeckFromDiscard($fromLocation);
        } else {
            self::reformDeckFromDiscard($fromLocation, false);
        }
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
        self::moveAllInLocation(LOCATION_CONNECTIONS_BLUE_FLIPPED, LOCATION_CONNECTIONS_BLUE_DISCARD);
        self::moveAllInLocation(LOCATION_CONNECTIONS_RED_FLIPPED, LOCATION_CONNECTIONS_RED_DISCARD);
    }

    public static function discard($id)
    {
        if (self::getDeck($id) === LOCATION_CONNECTIONS_BLUE_DECK) {
            self::move($id, LOCATION_CONNECTIONS_BLUE_DISCARD);
        } else {
            self::move($id, LOCATION_CONNECTIONS_RED_DISCARD);
        }
    }

    public static function flipForNewRound()
    {
        foreach ([
            LOCATION_CONNECTIONS_BLUE_DECK => LOCATION_CONNECTIONS_BLUE_FLIPPED,
            LOCATION_CONNECTIONS_RED_DECK => LOCATION_CONNECTIONS_RED_FLIPPED,
        ] as $location => $flipped) {
            self::pickForLocation(1, $location, $flipped);
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

    public static function getDeck(int $id): string
    {
        if (in_array(self::get($id)->getType(), array_keys(self::$blueCardTypes))) {
            return LOCATION_CONNECTIONS_RED_DECK;
        } else {
            return LOCATION_CONNECTIONS_BLUE_DECK;
        }
    }

    public static function getDeckName(int $id): string
    {
        if (self::getDeck($id) === LOCATION_CONNECTIONS_BLUE_DECK) {
            return clienttranslate('Blue');
        } else {
            return clienttranslate('Red');
        }
    }

    public static function reshuffle()
    {
        Notifications::connectionsReshuffle();
    }
}
