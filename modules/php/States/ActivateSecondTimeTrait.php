<?php

namespace STATE\States;

use STATE\Core\Stack;
use STATE\Managers\Locations;
use STATE\Managers\Players;

trait ActivateSecondTimeTrait
{
    public function argActivateSecondTime()
    {
        return ['locationId' => Stack::getCtx()['locationId']];
    }

    public function actActivateAgain()
    {
        Locations::get(Stack::getCtx()['locationId'])->activate(Players::getActive());
        Stack::finishState();
    }

    public function actDoNotActivateAgain()
    {
        Stack::finishState();
    }
}
