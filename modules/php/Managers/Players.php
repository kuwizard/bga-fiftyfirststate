<?php

namespace Bga\Games\Fiftyfirststate\Managers;

use Bga\Games\Fiftyfirststate\Game;
use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Preferences;
use Bga\Games\Fiftyfirststate\Helpers\Collection;
use Bga\Games\Fiftyfirststate\Helpers\DB_Manager;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Models\Player;

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
    public static function getAllNonPassed($exceptId = null)
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
        $playerIds = Game::get()->getObjectListFromDB("SELECT player_id FROM player ORDER BY {$orderByPlayer}player_no", true);
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

    public static function getAllFactionsUI()
    {
        $result = [];
        /** @var Player $player */
        foreach (self::getAll() as $player) {
            $result[$player->getId()] = [
                'color' => $player->getColor(),
                'faction' => self::getFactionUI($player->getFaction()),
                'side' => $player->getFactionSide(),
            ];
        }
        return $result;
    }

    public static function getAllFactions()
    {
        $result = [];
        /** @var Player $player */
        foreach (self::getAll() as $player) {
            $result[$player->getId()] = [
                'faction' => $player->getFaction(),
            ];
        }
        return $result;
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

    public static function setScoreAux(int $pId, int $value): void
    {
        self::DB()
            ->update(['player_score_aux' => $value])
            ->wherePlayer($pId)
            ->run();
    }

    public static function assignNewPreferredColorsToPlayers()
    {
        $preferences = Preferences::getPreferencesAll();
        $preferredFactions = self::getPreferredFactions($preferences);
        foreach ($preferredFactions as $pId => $faction) {
            self::get($pId)->setFaction($faction, self::getPreferredSide($preferences[$pId], $faction));
        }
        Game::get()->reloadPlayersBasicInfos();
    }

    private static function getPreferredFactions(array $preferences): array
    {
        $assignedFactions = [];
        $validPrefKeys = [NEW_YORK_PREFERENCE, APPALACHIAN_PREFERENCE, MUTANTS_PREFERENCE, MERCHANTS_PREFERENCE];

        $playerIdsWhoDontCare = [];
        foreach ($preferences as $pId => &$subArray) {
            // Filter valid preferences
            $validPreferences = array_intersect_key($subArray, array_flip($validPrefKeys));
            // Filter out players who decided not to choose preferences
            if (empty(array_diff(array_values($validPreferences), [0]))) {
                $playerIdsWhoDontCare[] = $pId;
            }
            // Flip them so we have priorities as keys and 201-204 as values
            $flippedPreferences = array_flip($validPreferences);
            ksort($flippedPreferences);

            $subArray = array_values($flippedPreferences);
        }
        // Remove all players who don't care
        $preferences = array_diff_key($preferences, array_flip($playerIdsWhoDontCare));

        $playersInPreferencesCount = count($preferences);
        for ($i = 0; $i < $playersInPreferencesCount - 1; $i++) {
            $players = array_keys($preferences);
            $player = $players[0];
            $factionToConsider = $preferences[$player][0];

            $playersWithSameFactionPreferred = array_filter($players, function ($player) use (
                $preferences,
                $factionToConsider
            ) {
                return $preferences[$player][0] === $factionToConsider;
            });

            $amountOfPlayersWithSameFactionPreferred = count($playersWithSameFactionPreferred);

            if ($amountOfPlayersWithSameFactionPreferred > 1) {
                shuffle($playersWithSameFactionPreferred);
                $player = $playersWithSameFactionPreferred[0];
            }
            $assignedFactions[$player] = self::convertPreferenceValueToRealFaction($factionToConsider);
            unset($preferences[$player]);

            $preferences = array_map(function ($subArray) use ($factionToConsider) {
                return array_values(
                    array_filter($subArray, function ($color) use ($factionToConsider) {
                        return $color !== $factionToConsider;
                    })
                );
            }, $preferences);
        }
        if (count($preferences) > 0) {
            $leftoverPlayer = array_keys($preferences)[0];
            $assignedFactions[$leftoverPlayer] = self::convertPreferenceValueToRealFaction($preferences[$leftoverPlayer][0]);
        }

        $factionsNeverChosen = array_values(array_diff(Factions::getAll(), array_values($assignedFactions)));
        shuffle($factionsNeverChosen);
        foreach ($playerIdsWhoDontCare as $playerId) {
            $assignedFactions[$playerId] = array_shift($factionsNeverChosen);
        }

        return $assignedFactions;
    }

    private static function getPreferredSide($preferences, $faction)
    {
        $validSides = [NEW_YORK_SIDE, APPALACHIAN_SIDE, MUTANTS_SIDE, MERCHANTS_SIDE];
        $preferences = array_map('intval', array_intersect_key($preferences, array_flip($validSides)));
        $result = [];
        foreach ($preferences as $sideValue => $preference) {
            $result[self::convertSideValueToRealFaction($sideValue)] = $preference;
        }
        return $result[$faction];
    }

    private static function convertPreferenceValueToRealFaction(int $value)
    {
        return [
            NEW_YORK_PREFERENCE => FACTION_NEW_YORK,
            APPALACHIAN_PREFERENCE => FACTION_APPALACHIAN,
            MUTANTS_PREFERENCE => FACTION_MUTANTS,
            MERCHANTS_PREFERENCE => FACTION_MERCHANTS,
        ][$value];
    }

    private static function convertSideValueToRealFaction(int $value)
    {
        return [
            NEW_YORK_SIDE => FACTION_NEW_YORK,
            APPALACHIAN_SIDE => FACTION_APPALACHIAN,
            MUTANTS_SIDE => FACTION_MUTANTS,
            MERCHANTS_SIDE => FACTION_MERCHANTS,
        ][$value];
    }

    public static function getFactionUI(int $faction): int
    {
        return ($faction / 10) - 50;
    }

    public static function giveEachPlayerCardsSetup(): void
    {
        /** @var Player $player */
        foreach (self::getAll() as $player) {
            $player->drawCards(GLOBAL_START_CARDS);
            Notifications::locationsDrawn($player, $player->getHand()->toArray(), true);
        }
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
