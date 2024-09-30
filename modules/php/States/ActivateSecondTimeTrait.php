<?php

namespace STATE\States;

use STATE\Core\Globals;
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
        if (!in_array(Stack::getCtx()['locationId'], array_keys(Players::getActive()->getPlayableLocationsWithCardWarnings()))) {
            $this->addConfirmIfNeeded();
            Stack::finishState();
        }
    }

    public function actActivateAgain()
    {
        $this->addConfirmIfNeeded();
        Locations::get(Stack::getCtx()['locationId'])->activate(Players::getActive());
        Stack::finishState();
    }

    public function actDoNotActivateAgain()
    {
        $this->addConfirmIfNeeded();
        Stack::finishState();
    }

    private function addConfirmIfNeeded()
    {
        if (Globals::isAddConfirmTurnEnd()) {
            Globals::setAddConfirmTurnEnd(false);
            Stack::insertOnTop(ST_CONFIRM_TURN_END);
        }
    }
}
