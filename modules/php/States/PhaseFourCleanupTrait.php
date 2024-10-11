<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Managers\Connections;
use STATE\Managers\Factions;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Player;

trait PhaseFourCleanupTrait
{
    public function stPhaseFourCleanup()
    {
        if (Globals::isLastRound()) {
            Notifications::message(clienttranslate('{highlight}Phase 4: Cleanup is skipped...'));
        } else {
            Notifications::message(clienttranslate('{highlight}Phase 4: Cleanup'));
            /** @var Player $player */
            foreach (Players::getAll() as $player) {
                Players::removeAllResources($player->getId());
                Locations::resetActivatedTimes($player->getBoard(true)->getIds());
                Notifications::playersResetAllResources();
            }
            Connections::discardFlippedEndOfRound();
            Players::resetAllPassed();
            Factions::resetAllUsed();
        }
        Stack::finishState();
    }
}
