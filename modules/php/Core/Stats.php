<?php
namespace Bga\Games\Fiftyfirststate\Core;

use Bga\Games\Fiftyfirststate\Game;
use Bga\Games\Fiftyfirststate\Models\Action;
use Bga\Games\Fiftyfirststate\Models\Feature;
use Bga\Games\Fiftyfirststate\Models\Location;
use Bga\Games\Fiftyfirststate\Models\Player;
use Bga\Games\Fiftyfirststate\Models\Production;

class Stats
{
    static $stats = [
        'common' => [
            STAT_TURNS_NUMBER,
            STAT_CHOSEN_NEW_YORK,
            STAT_CHOSEN_APPALACHIAN,
            STAT_CHOSEN_MUTANTS,
            STAT_CHOSEN_MERCHANTS,
            STAT_LOCATIONS_BUILT,
            STAT_LOCATIONS_DEAL,
            STAT_LOCATIONS_RAZED_FROM_HAND,
            STAT_LOCATIONS_RAZED_OPPONENTS,
            STAT_LOCATIONS_USED_OPEN_PROD,
            STAT_LOCATIONS_DEVELOPED,
            STAT_PRODUCE_FUEL,
            STAT_PRODUCE_GUN,
            STAT_PRODUCE_IRON,
            STAT_PRODUCE_BRICK,
            STAT_PRODUCE_WORKER,
            STAT_PRODUCE_AMMO,
            STAT_PRODUCE_DEVEL,
            STAT_PRODUCE_ARROW_GREY,
            STAT_PRODUCE_ARROW_RED,
            STAT_PRODUCE_ARROW_BLUE,
            STAT_PRODUCE_ARROW_UNIVERSAL,
            STAT_TAKEN_LOCATIONS,
            STAT_TAKEN_CONNECTIONS,
            STAT_PRODUCTION_BUILT,
            STAT_FEATURES_BUILT,
            STAT_ACTIONS_BUILT,
            STAT_SPENT_WORKERS_TO_GET_FUEL,
            STAT_SPENT_WORKERS_TO_GET_GUN,
            STAT_SPENT_WORKERS_TO_GET_IRON,
            STAT_SPENT_WORKERS_TO_GET_BRICK,
            STAT_SPENT_WORKERS_TO_GET_LOCATION,
        ],
        'table' => [
        ],
        'player' => [
            STAT_PLAYER_FEATURES_ACTIVATED,
            STAT_PLAYER_ACTIONS_ACTIVATED,
            STAT_PLAYER_VICTIM_OF_RAZE,
            STAT_PLAYER_SCORE_DURING_GAME,
            STAT_PLAYER_SCORE_LOCATIONS,
            STAT_PLAYER_TOTAL_SCORE,
        ],
    ];

    public static function setupNewGame()
    {
        Game::get()->bga->playerStats->init(static::$stats['common'], 0, true);
//        Game::get()->bga->tableStats->init(static::$stats['table'], 0);
        Game::get()->bga->playerStats->init(static::$stats['player'], 0);
    }

    public static function setTable(string $statName, int|float|bool $value): void
    {
        static::assertTableStatExists($statName);
        Game::get()->bga->tableStats->set($statName, $value);
    }

    public static function incTable(string $statName, int $delta = 1): void
    {
        static::assertTableStatExists($statName);
        Game::get()->bga->tableStats->inc($statName, $delta);
    }

    public static function setPlayer(int|Player $player, string $statName, int|float|bool $value): void
    {
        $playerId = static::extractPlayerId($player);
        Game::get()->bga->playerStats->set($statName, $value, $playerId);
    }

    public static function incPlayer(int|Player $player, string $statName, int $delta = 1): void
    {
        $playerId = static::extractPlayerId($player);
        $isCommon = in_array($statName, static::$stats['common'], true);
        Game::get()->bga->playerStats->inc($statName, $delta, $playerId, $isCommon);
    }

    private static function assertTableStatExists(string $statName): void
    {
        if (!in_array($statName, static::$stats['table'], true)) {
            throw new \InvalidArgumentException("Unknown table stat: {$statName}");
        }
    }

    private static function extractPlayerId(int|Player $player): int
    {
        if ($player instanceof Player) {
            return $player->getId();
        }

        return (int) $player;
    }

    public static function applyFactions(array $allFactions)
    {
        $factionsMap = [
            FACTION_NEW_YORK => STAT_CHOSEN_NEW_YORK,
            FACTION_APPALACHIAN => STAT_CHOSEN_APPALACHIAN,
            FACTION_MUTANTS => STAT_CHOSEN_MUTANTS,
            FACTION_MERCHANTS => STAT_CHOSEN_MERCHANTS,
        ];
        foreach ($allFactions as $pId => $info) {
            self::incPlayer($pId, $factionsMap[$info['faction']]);
        }
    }

    public static function getResource(Player $player, int $type, int $amount)
    {
        $resourcesMap = [
            RESOURCE_FUEL => STAT_PRODUCE_FUEL,
            RESOURCE_GUN => STAT_PRODUCE_GUN,
            RESOURCE_IRON => STAT_PRODUCE_IRON,
            RESOURCE_BRICK => STAT_PRODUCE_BRICK,
            RESOURCE_AMMO => STAT_PRODUCE_AMMO,
            RESOURCE_ARROW_GREY => STAT_PRODUCE_ARROW_GREY,
            RESOURCE_ARROW_RED => STAT_PRODUCE_ARROW_RED,
            RESOURCE_ARROW_BLUE => STAT_PRODUCE_ARROW_BLUE,
            RESOURCE_ARROW_UNIVERSAL => STAT_PRODUCE_ARROW_UNIVERSAL,
            RESOURCE_WORKER => STAT_PRODUCE_WORKER,
            RESOURCE_VP => STAT_PLAYER_SCORE_DURING_GAME,
            RESOURCE_DEVELOPMENT => STAT_PRODUCE_DEVEL,
        ];
        if (isset($resourcesMap[$type])) {
            self::incPlayer($player, $resourcesMap[$type], $amount);
        }
    }

    public static function getResourceForWorkers(Player $player, int $type)
    {
        $resourcesMap = [
            RESOURCE_FUEL => STAT_SPENT_WORKERS_TO_GET_FUEL,
            RESOURCE_GUN => STAT_SPENT_WORKERS_TO_GET_GUN,
            RESOURCE_IRON => STAT_SPENT_WORKERS_TO_GET_IRON,
            RESOURCE_BRICK => STAT_SPENT_WORKERS_TO_GET_BRICK,
            RESOURCE_CARD => STAT_SPENT_WORKERS_TO_GET_LOCATION,
        ];
        if (isset($resourcesMap[$type])) {
            self::incPlayer($player, $resourcesMap[$type]);
        }
    }

    public static function locationBuilt(Player $player, Location $location)
    {
        Stats::incPlayer($player, STAT_LOCATIONS_BUILT);

        $stat = '';
        if ($location instanceof Production) {
            $stat = STAT_PRODUCTION_BUILT;
        } elseif ($location instanceof Feature) {
            $stat = STAT_FEATURES_BUILT;
        } elseif ($location instanceof Action) {
            $stat = STAT_ACTIONS_BUILT;
        }
        if ($stat !== '') {
            Stats::incPlayer($player, $stat);
        }
    }
}

?>
