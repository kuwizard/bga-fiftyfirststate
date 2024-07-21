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

    /**
     * @param Player $player
     * @return void
     */
    public static function draw($player)
    {
        $top = self::getTopOf(LOCATION_DECK);
        $top->action($player);
        self::move($top->getId(), LOCATION_DECK, LOCATION_DISCARD);
//        Notifications::connectionDrawn($player);
    }

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
}
