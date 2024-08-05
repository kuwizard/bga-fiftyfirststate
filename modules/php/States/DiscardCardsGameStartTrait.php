<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Managers\Players;

trait DiscardCardsGameStartTrait
{
    /**
     * @param int[] $locationsIds
     * @return void
     */
    public function actDiscardCardsGameStart($locationsIds)
    {
        self::checkAction('actDiscardCardsGameStart');
        $currentPlayer = Players::getCurrent();
        $currentPlayer->discard($locationsIds);
        Notifications::handChanged($currentPlayer);
        Notifications::resourcesChanged($currentPlayer, ['cards' => $currentPlayer->getHandAmount()]);
        $this->gamestate->setPlayerNonMultiactive($currentPlayer->getId(), '');
    }
}
