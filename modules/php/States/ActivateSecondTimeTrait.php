<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;

trait ActivateSecondTimeTrait
{
    public function argActivateSecondTime()
    {
        return [
            'locationId' => Stack::getCtx()['locationId'] ?? null,
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
        ];
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
        $ctx = Stack::getCtx();
        if ($ctx['state'] === ST_ACTIVATE_SECOND_TIME) {
            Locations::get($ctx['locationId'])->activate(Players::getActive());
            Stack::finishState();
        } else if ($ctx['state'] === ST_ACTIVATE_SPEND_WORKERS_AGAIN) {
            $this->actSpendWorkers();
        } else {
            throw new \BgaVisibleSystemException('Incorrect state to activate again: ' . $ctx['state']);
        }
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
