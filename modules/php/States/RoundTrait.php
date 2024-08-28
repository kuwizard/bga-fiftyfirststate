<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Stack;
use STATE\Managers\Players;

trait RoundTrait
{
    public function stNextRound()
    {
        $firstPlayer = Globals::getFirstPlayerId();
        if ($firstPlayer === 0) {
            $nextPlayer = Players::getFirstFirstPlayerId();
        } else {
            $nextPlayer = Players::getNextId($firstPlayer);
        }
        Globals::setFirstPlayerId($nextPlayer);

        $stack = [
            ST_PHASE_ONE_LOOKOUT_SETUP,
            ST_PHASE_TWO_PRODUCTION,
            ST_PHASE_THREE_ACTION,
            ST_CHOOSE_RESOURCE_TO_STORE,
            ST_PHASE_FOUR_CLEANUP,
            ST_NEXT_ROUND,
        ];

        $this->gamestate->changeActivePlayer($nextPlayer);
        self::giveExtraTime($nextPlayer);
        Stack::setup($stack);
        Stack::finishState();
    }
}
