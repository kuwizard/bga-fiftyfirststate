<?php
namespace Bga\Games\Fiftyfirststate\Core;

use Bga\Games\Fiftyfirststate\Game;
use Bga\Games\Fiftyfirststate\Models\Player;

class Stats
{
    protected static function init($type, $name, $value = 0)
    {
        Game::get()->initStat($type, $name, $value);
    }

    protected static function inc($name, $player = null, $value = 1)
    {
        $pId = is_null($player) ? null : ($player instanceof Player ? $player->getId() : $player);
        Game::get()->incStat($value, $name, $pId);
    }

    protected static function get($name, $player = null)
    {
        return Game::get()->getStat($name, $player);
    }

    protected static function set($value, $name, $player = null)
    {
        $pId = is_null($player) ? null : ($player instanceof Player ? $player->getId() : $player);
        Game::get()->setStat($value, $name, $pId);
    }

    public static function setupNewGame()
    {
        $stats = Game::get()->getStatTypes();

        foreach ($stats['table'] as $key => $value) {
            if ($value['id'] > 10) {
                self::init('table', $key);
            }
        }

        foreach ($stats['player'] as $key => $value) {
            if ($value['id'] > 100) {
                self::init('player', $key);
            }
        }
    }
}

?>
