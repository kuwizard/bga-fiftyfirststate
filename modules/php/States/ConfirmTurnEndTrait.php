<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Managers\Players;

trait ConfirmTurnEndTrait
{
    public function argConfirmTurnEnd()
    {
        $player = Players::getActive();
        $showPassNextTurn = Stack::getCtx()['showPassNextTurn'] ?? true;
        return [
            'mayPlaceDefence' => $player->getResource(RESOURCE_DEFENCE) > 0 && $player->getBoard()->count() > 0,
            'forceTimer' => Stack::getCtx()['forceTimer'] ?? false,
            'willPlayNextTurn' => $showPassNextTurn ? Globals::willPlayNextTurn() : null,
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
