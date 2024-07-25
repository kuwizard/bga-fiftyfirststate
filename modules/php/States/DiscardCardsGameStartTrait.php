<?php

namespace STATE\States;

use STATE\Managers\Players;

trait DiscardCardsGameStartTrait
{
    /**
     * @param int[] $cardIds
     * @return void
     */
    public function actDiscardCardsGameStart($cardIds)
    {
        self::checkAction('actDiscardCardsGameStart');
        $currentPlayer = Players::getCurrent();
        $currentPlayer->discard($cardIds);
        $this->gamestate->setPlayerNonMultiactive($currentPlayer->getId(), '');
    }
}
