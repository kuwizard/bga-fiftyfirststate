<?php

namespace STATE\States;

use STATE\Core\Stack;
use STATE\Managers\Connections;
use STATE\Managers\Locations;
use STATE\Managers\Players;

trait PhaseFourCleanupTrait
{
    public function stPhaseFourCleanup()
    {
        Locations::resetActivatedTimes();
        Connections::discardFlippedEndOfRound();
        Players::resetAllPassed();
        Stack::finishState();
    }
}
