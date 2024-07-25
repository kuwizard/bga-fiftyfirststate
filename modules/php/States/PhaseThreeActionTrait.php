<?php

namespace STATE\States;

use STATE\Core\Stack;
use STATE\Managers\Players;

trait PhaseThreeActionTrait
{
    public function actActionPass()
    {
        self::checkAction('actActionPass');
        Players::getActive()->markAsPassed();
        if (Players::isAllPassed()) {
            Stack::unsuspendNext(ST_PHASE_THREE_ACTION);
        }
        Stack::finishState();
    }

    public function actDoSomething()
    {
        self::checkAction('actDoSomething');
        Stack::finishState();
    }
}
