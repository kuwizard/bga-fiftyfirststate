<?php

namespace STATE\Managers;

use STATE\Core\Game;
use STATE\Core\Globals;
use STATE\Helpers\Collection;
use STATE\Helpers\DB_Manager;
use STATE\Helpers\ResourcesHelper;
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
     * @return Player
     */
    public static function getActive()
    {
        return self::get(self::getActiveId());
    }

    /**
     * @return int
     */
    public static function getCurrentId()
    {
        return (int) Game::get()->getCurrentPId();
    }

    /**
     * @param int $exceptId
     * @return Collection
     */
    public static function getAll($exceptId = null)
    {
        $all = self::DB()->get();
        return $exceptId
            ? $all->filter(function ($player) use ($exceptId) {
                return $player->getId() !== $exceptId;
            })
            : $all;
    }

    /**
     * @param int $exceptId
     * @return Collection
     */
    public static function getAllNonPassed($exceptId)
    {
        return self::getAll($exceptId)->filter(function ($player) {
            return !$player->isPassed();
        });
    }

    /**
     * @param Player $startWith
     * @return int[]
     */
    public static function getPlayerIdsSortedByNo($startWith = null)
    {
        $orderByPlayer = $startWith ? "player_no < {$startWith->getNo()}, " : '';
        $playerIds = self::getObjectListFromDB("SELECT player_id FROM player ORDER BY {$orderByPlayer}player_no", true);
        return array_map(function ($pId) {
            return (int) $pId;
        }, $playerIds);
    }

    /**
     * @param int $pId
     * @return Player
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

    /**
     * @return int
     */
    public static function getFirstFirstPlayerId()
    {
        return self::getByNo(1)->getId();
    }

    public static function getFirstPlayerId()
    {
        return Globals::getFirstPlayerId();
    }

    private static function getByNo($no)
    {
        return self::DB()
            ->where('player_no', $no)
            ->getSingle();
    }


    public static function getNextId($player = null)
    {
        if ($player) {
            $pId = is_int($player) ? $player : $player->getId();
        } else {
            $pId = self::getActiveId();
        }

        $nextId = Game::get()->getNextPlayerTable()[$pId];
        $i = 0;
        while (self::get($nextId)->isPassed() && $i < 5) {
            $nextId = Game::get()->getNextPlayerTable()[$nextId];
            $i = $i + 1;
        }
        return $nextId;
    }

    /**
     * @return bool
     */
    public static function isAllPassed()
    {
        return self::DB()
                ->where('player_passed', 0)
                ->count() === 0;
    }

    public static function markAsPassed(int $pId)
    {
        self::DB()
            ->wherePlayer($pId)
            ->update(['player_passed' => 1])
            ->run();
    }

    public static function resetAllPassed()
    {
        self::DB()
            ->update(['player_passed' => 0])
            ->run();
    }

    public static function removeAllResources(int $pId)
    {
        $resourcesWithNames = array_map(function ($resource) {
            return ResourcesHelper::getDBName($resource);
        }, ALL_RESOURCES_LIST);
        self::DB()
            ->wherePlayer($pId)
            ->update(array_fill_keys($resourcesWithNames, 0))
            ->run();
    }

    /**
     * @param int $id
     * @return Player|null
     */
    public static function getOwner(int $id)
    {
        /** @var Player $player */
        foreach (self::getAll() as $player) {
            if (in_array($id, $player->getBoard()->getIds())) {
                return $player;
            }
        }
        return null;
    }

    /**
     * @param int $pId
     * @param int $value
     * @return void
     */
    public static function setScoreAux($pId, $value)
    {
        self::DB()
            ->update(['player_score_aux' => $value])
            ->wherePlayer($pId)
            ->run();
    }

    /**
     * @param $pId
     * @return Collection
     */
    public static function getUiData($pId)
    {
        return self::getAll()->map(function ($player) use ($pId) {
            return $player->jsonSerialize($pId);
        });
    }
}
