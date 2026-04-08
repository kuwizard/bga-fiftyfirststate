<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Managers\Resources;
use Bga\Games\Fiftyfirststate\Models\FeatureStorageMultiple;
use Bga\Games\Fiftyfirststate\Models\Location;
use Bga\Games\Fiftyfirststate\Models\Player;

trait PhaseTwoProductionTrait
{
    public function stPhaseTwoProduction()
    {
        Notifications::message(clienttranslate('{highlight}Phase 2: Production'));
        /** @var Player $player */
        foreach (Players::getAll() as $player) {
            $resourcesFromCards = $this->getResourcesFromCards($player, true);
            $factionProd = $player->getFactionProduction();
            $dealsProd = $player->getDeals();
            $prodLocations = $player->getProduction();
            $combinedResources = array_count_values(array_merge($factionProd, $dealsProd, $prodLocations));
            $player->increaseResources($combinedResources);
            $combinedPlusFromCards = array_unique(array_merge(array_keys($combinedResources), array_keys($resourcesFromCards)));
            Notifications::resourcesChanged($player, $player->getResourcesWithNames($combinedPlusFromCards));
            Notifications::locationsDrawn($player);
            Notifications::deckChanged();
            if (!empty($factionProd)) {
                Notifications::playerPhaseTwoProductionFaction($player, $factionProd);
            }
            if (!empty($prodLocations)) {
                Notifications::playerPhaseTwoProductionLocations($player, $prodLocations);
            }
            if (!empty($dealsProd)) {
                Notifications::playerPhaseTwoDeals($player, $dealsProd);
            }
            if (Globals::getLastRoundNotify()) {
                Globals::setLastRoundNotify(false);
                Notifications::lastRound($player);
            }
        }
        Notifications::message(clienttranslate('{highlight}Phase 3: Action'));
        Stack::finishState();
    }

    public function getResourcesFromCards(Player $player, bool $sendNotification = false)
    {
        /** @var Location $location */
        $storageLocations = $player->getBoard()->filter(function ($location) {
            return $location instanceof FeatureStorageMultiple;
        });
        $resourcesFromCards = [];
        /** @var FeatureStorageMultiple $location */
        foreach ($storageLocations as $location) {
            $resources = $location->getResources();
            if (!empty($resources)) {
                ResourcesHelper::increaseResourcesAfterAction($player, $resources);
                Resources::deleteAll($location->getId());
                $resourcesToNotify = [];
                foreach (array_count_values($resources) as $resource => $amount) {
                    $resourcesFromCards[$resource] = $amount;
                    $resourcesToNotify[ResourcesHelper::getResourceName($resource)] = $amount;
                }
                if ($sendNotification) {
                    Notifications::playerGotResourcesFromStorage($player, $location, $resourcesToNotify);
                }
            }
        }
        return $resourcesFromCards;
    }
}
