<?php

namespace STATE\Managers;

use STATE\Helpers\Pieces;
use STATE\Models\Connection;
use STATE\Models\Player;

class Connections extends Pieces
{
    protected static $table = 'connections';
    protected static $primary = 'connection_id';
    protected static $prefix = 'connection_';
    protected static $customFields = ['type'];

    protected static function cast($row)
    {
        return new Connection($row);
    }

    private static $blueCardTypes = [
        'JunkTrain',
        'Merchants',
    ];

    private static $redCardTypes = [
        'Punks',
        'Thugs',
    ];

    public static function setupNewGame()
    {
        foreach ([
            'blueCardTypes' => LOCATION_CONNECTIONS_BLUE_DECK,
            'redCardTypes' => LOCATION_CONNECTIONS_RED_DECK,
        ] as $deck => $location) {
            foreach (self::$$deck as $class) {
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

    public static function get($id = null, $raiseExceptionIfNotEnough = true)
    {
        return self::DB()
            ->where($id)
            ->getSingle();
    }

    public static function discardFlippedEndOfRound()
    {
        foreach ([LOCATION_CONNECTIONS_BLUE_FLIPPED, LOCATION_CONNECTIONS_RED_FLIPPED] as $location) {
            self::moveAllInLocation($location, LOCATION_DISCARD);
        }
    }

    /**
     * @param Player $player
     * @param boolean $isBlue
     * @return void
     */
    public static function draw($player, $isBlue)
    {
        $top = $isBlue ?
            self::getTopOf(LOCATION_CONNECTIONS_BLUE_FLIPPED) :
            self::getTopOf(LOCATION_CONNECTIONS_RED_DECK);
        if (!$top) {
            throw new \BgaVisibleSystemException("Tried to get a top of flipped deck (is blue: $isBlue) but it's empty");
        }
        $top->action($player);
        self::move($top->getId(), LOCATION_DISCARD);
//        Notifications::connectionDrawn($player);
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
}
