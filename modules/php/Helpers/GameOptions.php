<?php

namespace Bga\Games\Fiftyfirststate\Helpers;

use Bga\Games\Fiftyfirststate\Game;

class GameOptions
{
    public static function getExpansion()
    {
        return (int) Game::get()->getGameStateValue('expansion');
    }
}
