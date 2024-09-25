<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Players;
use STATE\Managers\Resources;
use STATE\Models\FeatureStorageMultiple;
use STATE\Models\Location;
use STATE\Models\Player;

trait PhaseTwoProductionTrait
{
    public function stPhaseTwoProduction()
    {
        /** @var Player $player */
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
            
            $factionProd = $player->getFactionProduction();
            $dealsProd = $player->getDeals();
            $prodLocations = $player->getProduction();
            $combinedResources = array_count_values(array_merge($factionProd, $dealsProd, $prodLocations));
            $player->increaseResources($combinedResources);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames(array_keys($combinedResources)));
            Notifications::handChanged($player);
            Notifications::deckChanged();
        }
        Stack::finishState();
    }
}
