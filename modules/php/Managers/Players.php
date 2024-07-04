<?php

namespace STATE\Managers;

use STATE\Core\Game;
use STATE\Helpers\DB_Manager;
use STATE\Models\Player;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class Players extends DB_Manager
{
    protected static $table = 'player';
    protected static $primary = 'player_id';

    protected static function cast($row)
    {
        return new Player($row);
    }

    public static function setupNewGame($players, $options)
    {
        // Create players
        $gameInfos = Game::get()->getGameinfos();
        $colors = $gameInfos['player_colors'];
        shuffle($colors);
        $query = self::DB()->multipleInsert([
            'player_id',
            'player_color',
            'player_canal',
            'player_name',
            'player_avatar',
        ]);

        $values = [];
        foreach ($players as $pId => $player) {
            $color = array_shift($colors);
            $values[] = [
                $pId,
                $color,
                $player['player_canal'],
                $player['player_name'],
                $player['player_avatar'],
            ];
        }
        $query->values($values);
    }

    public static function getActiveId()
    {
        return Game::get()->getActivePlayerId();
    }

    public static function getCurrentId()
    {
        return (int)Game::get()->getCurrentPId();
    }

    public static function getAll()
    {
        return self::DB()->get(false);
    }

    /*
     * get : returns the Player object for the given player ID
     */
    public static function get($pId = null)
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
