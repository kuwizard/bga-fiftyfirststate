<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Stack;

trait ConfirmTurnEndTrait
{
    public function actConfirmTurnEnd()
    {
        Stack::finishState();
    }

    public function actResetTurn()
    {
        $this->undoRestorePoint();
        $this->gamestate->reloadState();
    }
}
