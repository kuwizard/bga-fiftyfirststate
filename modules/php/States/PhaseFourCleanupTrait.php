<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Connections;
use STATE\Managers\Factions;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Managers\Resources;
use STATE\Models\FeatureStorageMultiple;
use STATE\Models\Location;
use STATE\Models\Player;

trait PhaseFourCleanupTrait
{
    public function stPhaseFourCleanup()
    {
        if (!Globals::isLastRound()) {
            /** @var Player $player */
            foreach (Players::getAll() as $player) {
                Players::removeAllResources($player->getId());
                Locations::resetActivatedTimes($player->getBoard()->getIds());
                Notifications::playersResetAllResources();
            }
            foreach (Players::getAll() as $player) {
                /** @var Location $location */
                $storageLocations = $player->getBoard()->filter(function ($location) {
                    return $location instanceof FeatureStorageMultiple;
                });
                /** @var FeatureStorageMultiple $location */
                foreach ($storageLocations as $location) {
                    $resources = $location->getResources();
                    ResourcesHelper::increaseResourcesAfterAction($player, $resources);
                    Resources::deleteAll($location->getId());
                    Notifications::playerGotResourcesFromStorage(
                        $player,
                        $location->getId(),
                        $player->getResourcesWithNames(array_unique($resources))
                    );
                }
            }
            Connections::discardFlippedEndOfRound();
            Players::resetAllPassed();
            Factions::resetAllUsed();
        }
        Stack::finishState();
    }
}
