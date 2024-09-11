<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Managers\Locations;
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
        Notifications::resourcesChanged($currentPlayer, ['card' => $currentPlayer->getHandAmount()]);
        foreach ($locationsIds as $locationId) {
            Notifications::locationDiscarded(
                $currentPlayer,
                Locations::get($locationId),
                Locations::countInLocation(LOCATION_DISCARD)
            );
        }
        $this->gamestate->setPlayerNonMultiactive($currentPlayer->getId(), '');
    }
}
