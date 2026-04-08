<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Stack;
use STATE\Managers\Players;

trait ConfirmTurnEndTrait
{
    public function argConfirmTurnEnd()
    {
        $player = Players::getActive();
        return [
            'mayPlaceDefence' => $player->getResource(RESOURCE_DEFENCE) > 0 && $player->getBoard()->count() > 0,
            'forceTimer' => Stack::getCtx()['forceTimer'] ?? false,
        ];
    }

    public function stConfirmTurnEnd()
    {
        Globals::setActionDone(true);
    }

    public function actConfirmTurnEnd()
    {
        Globals::setActionDone(false);
        Stack::finishState();
    }

    public function actResetTurn()
    {
        $this->undoRestorePoint();
        $this->gamestate->reloadState();
    }
}
