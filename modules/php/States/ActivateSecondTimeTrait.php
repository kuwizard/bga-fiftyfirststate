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

    public function stActivateSecondTime()
    {
        // Happens when after first activation there's not enough resources to activate second time
        if (!in_array(Stack::getCtx()['locationId'], Players::getActive()->getPlayableLocationsIds())) {
            Stack::finishState();
        }
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
