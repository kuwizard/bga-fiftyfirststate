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

    /**
     * @return int
     */
    public static function getActiveId()
    {
        return (int) Game::get()->getActivePlayerId();
    }

    /**
     * @return int
     */
    public static function getCurrentId()
    {
        return (int) Game::get()->getCurrentPId();
    }

    public static function getAll()
    {
        return self::DB()->get();
    }

    /**
     * @param boolean $backwards
     * @return int[]
     */
    public static function getPlayersSortedByNo($backwards = false)
    {
        $sort = $backwards ? 'DESC' : 'ASC';
        $playerIds = self::getObjectListFromDB("SELECT player_id FROM player ORDER BY player_no {$sort}", true);
        return array_map(function ($pId) {
            return (int) $pId;
        }, $playerIds);
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

    /**
     * @return Player | null
     */
    public static function getCurrent()
    {
        return self::get(self::getCurrentId());
    }

//    private static function getNextNonZombie($startFrom = 0)
//    {
//        $increment = 1;
//        $probablyNext = self::getByNo($startFrom + $increment);
//        while ($probablyNext && $probablyNext->isZombie()) {
//            $increment++;
//            $probablyNext = self::getByNo($startFrom + $increment);
//        }
//        return $probablyNext;
//    }
//

    public static function getFirstPlayerId()
    {
        return self::getByNo(1)->getId();
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
