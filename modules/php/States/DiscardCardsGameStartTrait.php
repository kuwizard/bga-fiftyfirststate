<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Managers\Locations;
use STATE\Managers\Players;

trait DiscardCardsGameStartTrait
{
    public function actDiscardCardsGameStart(string $locationsIds): void
    {
        $locationsIdsArray = array_map(function ($locationsId) {
            return (int) $locationsId;
        }, explode(';', $locationsIds));
        $currentPlayer = Players::getCurrent();
        $currentPlayer->discard($locationsIdsArray);
        Notifications::resourcesChanged($currentPlayer, ['card' => $currentPlayer->getHandAmount()]);
        foreach ($locationsIdsArray as $locationId) {
            Notifications::locationDiscarded($currentPlayer, Locations::get($locationId));
        }
        Notifications::discardTwoCards($currentPlayer);
        $this->gamestate->setPlayerNonMultiactive($currentPlayer->getId(), '');
    }
}
