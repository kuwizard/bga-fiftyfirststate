<?php

namespace STATE\States;

use STATE\Core\Notifications;
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
        Notifications::resourcesChanged($currentPlayer, ['cards' => $currentPlayer->getHandAmount()]);
        $this->gamestate->setPlayerNonMultiactive($currentPlayer->getId(), '');
    }
}
