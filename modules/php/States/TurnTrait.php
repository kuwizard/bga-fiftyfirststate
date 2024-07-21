<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Stack;
use STATE\Managers\Players;

trait TurnTrait
{
    public function stNextTurn()
    {
        $nextPlayer = Players::getNextId(Globals::getFirstPlayerId());
        Globals::setFirstPlayerId($nextPlayer);

        $stack = [
            ST_PHASE_ONE_LOOKOUT_SETUP,
            98,
            ST_NEXT_TURN,
        ];

        $this->gamestate->changeActivePlayer($nextPlayer);
        self::giveExtraTime($nextPlayer);
        Stack::setup($stack);
        Stack::finishState();
    }
}
