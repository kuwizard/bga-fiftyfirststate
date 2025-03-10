<?php

namespace STATE\Helpers;

use STATE\Core\Game;

class GameOptions
{
    public static function getExpansion()
    {
        return (int) Game::get()->getGameStateValue('expansion');
    }
}
