<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Managers\Connections;
use Bga\Games\Fiftyfirststate\Managers\Factions;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\Player;

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
            Locations::resetAllDefended();
        }
        Stack::finishState();
    }
}
