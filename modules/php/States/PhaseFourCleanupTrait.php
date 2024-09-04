<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Stack;
use STATE\Managers\Connections;
use STATE\Managers\Factions;
use STATE\Managers\Players;

trait PhaseFourCleanupTrait
{
    public function stPhaseFourCleanup()
    {
        if (!Globals::isLastRound()) {
            Connections::discardFlippedEndOfRound();
            Players::resetAllPassed();
            Factions::resetAllUsed();
        }
        Stack::finishState();
    }
}
