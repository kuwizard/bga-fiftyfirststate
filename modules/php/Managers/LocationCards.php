<?php

namespace STATE\Managers;

use STATE\Core\Game;
use STATE\Helpers\Pieces;
use STATE\Models\LocationCard;
use STATE\Models\Player;

class LocationCards extends Pieces
{
    protected static $table = 'cards';
    protected static $primary = 'card_id';
    protected static $prefix = 'card_';
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
        return new LocationCard($row);
    }

    private static $allCardTypes = [
        'FuelTank',
        'Crossroads',
        'OilmenFortress',
    ];

    public static function setupNewGame()
    {
        foreach (self::$allCardTypes as $class) {
            $name = "STATE\Data\Cards\\" . $class;
            /** @var LocationCard $card */
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

    public static function getActiveId()
    {
        return Game::get()->getActivePlayerId();
    }

    public static function getCurrentId()
    {
        return (int) Game::get()->getCurrentPId();
    }

    public static function getAll()
    {
        return self::DB()->get();
    }

    /*
     * get : returns the Player object for the given player ID
     */
    public static function get($pId = null, $raiseExceptionIfNotEnough = true)
    {
        $pId = $pId ?: self::getActiveId();
        return self::DB()
            ->where($pId)
            ->getSingle();
    }

    public static function getCurrent()
    {
        return self::get(self::getCurrentId());
    }

    public static function getGuesser()
    {
        return self::DB()
            ->where('player_is_guesser', 1)
            ->getSingle();
    }

    public static function getNonGuessers()
    {
        return self::DB()
            ->where('player_is_guesser', 0)
            ->get();
    }

    public static function switchGuesser()
    {
        $newGuesser = self::getNextAfterGuesser();
        $newGuesser->setAsGuesser();
        return $newGuesser;
    }

    public static function getNextAfterGuesser()
    {
        $guesser = self::getGuesser();
        if (is_null($guesser)) {
            $next = self::getByNo(1);
        } else {
            $guesserNo = $guesser->getNo();
            $next = self::getNextNonZombie($guesserNo);
            if (!$next) {
                $next = self::getNextNonZombie();
            }
        }
        return $next;
    }

    private static function getNextNonZombie($startFrom = 0)
    {
        $increment = 1;
        $probablyNext = self::getByNo($startFrom + $increment);
        while ($probablyNext && $probablyNext->isZombie()) {
            $increment++;
            $probablyNext = self::getByNo($startFrom + $increment);
        }
        return $probablyNext;
    }

    private static function getByNo($no)
    {
        return self::DB()
            ->where('player_no', $no)
            ->getSingle();
    }

    public static function getNextId($player)
    {
        $pId = is_int($player) ? $player : $player->getId();
        $table = Game::get()->getNextPlayerTable();
        return $table[$pId];
    }

    /*
     * getUiData : get all ui data of all players
     */
    public static function getUiData($pId)
    {
        return self::getAll()->map(function ($player) use ($pId) {
            return $player->jsonSerialize($pId);
        });
    }
}
