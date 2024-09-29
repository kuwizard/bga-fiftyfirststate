<?php

namespace STATE\States;

use STATE\Core\Stack;

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
