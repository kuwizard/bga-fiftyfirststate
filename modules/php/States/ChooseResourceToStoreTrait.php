<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\Collection;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Players;
use STATE\Managers\Resources;
use STATE\Models\Feature;
use STATE\Models\Location;
use STATE\Models\Player;

trait ChooseResourceToStoreTrait
{
    public function argChooseResourceToStore()
    {
        $args = [];
        /** @var Player $player */
        foreach ($this->getPlayersWithStoreFeatures()->toArray() as $player) {
            $nonZeroResources = $this->getPlayersAvailableResources($player, $this->getResourcesAvailableToStore());
            $args[$player->getId()] = ResourcesHelper::getResourceNames($nonZeroResources);
        }
        return ['_private' => $args];
    }

    public function stChooseResourceToStore()
    {
        $players = $this->getPlayersWithStoreFeatures()->getIds();
        $this->gamestate->setPlayersMultiactive($players, '');
    }

    public function actPassStoringResource()
    {
        self::checkAction('actPassStoringResource');
        $this->gamestate->setPlayerNonMultiactive(Players::getCurrentId(), '');
    }

    public function actChooseResourceToStore($resourceName)
    {
        self::checkAction('actChooseResourceToStore');
        $player = Players::getCurrent();
        $resourceType = ResourcesHelper::getResourceType($resourceName);
        $player->decreaseResource($resourceType);
        $notificationData = [$resourceName => $player->getResource($resourceType)];
        Notifications::resourcesChanged($player, $notificationData);
        /** @var Location $location */
        $location = $this->getFirstStorageLocation($player);
        $location->addResource($resourceType);
        Notifications::resourcesPlacedOnLocation(
            $player,
            $location->getId(),
            ResourcesHelper::getResourceNames([$resourceType])
        );
        if (!empty($this->getPlayersAvailableResources($player, $this->getResourcesAvailableToStore()))
            && !is_null($this->getFirstStorageLocation($player))) {
            Stack::insertOnTop(ST_CHOOSE_RESOURCE_TO_STORE);
        }
        Stack::finishState();
    }

    /**
     * @param Player $player
     * @param int[] $resourcesTypes
     * @return int[]
     */
    private function getPlayersAvailableResources($player, $resourcesTypes)
    {
        /** @var Player $player */
        return array_values(array_filter($resourcesTypes, function ($resource) use ($player) {
            return $player->getResource($resource) > 0;
        }));
    }

    /**
     * @return Collection
     */
    private function getPlayersWithStoreFeatures()
    {
        return Players::getAll()->filter(function (Player $player) {
            return
                !$player->getBoard()->filter(function ($location) {
                    return $location instanceof Feature && $location->getFeatureType() === FEATURE_STORE_RESOURCES;
                })->empty();
        });
    }

    /**
     * @return int[]
     */
    private function getResourcesAvailableToStore()
    {
        // TODO: Expansions: check which locations store what and send only those resources
        return [RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK];
    }

    /**
     * @param $player
     * @return Location | null
     */
    private function getFirstStorageLocation($player)
    {
        return $player->getBoard()->filter(function ($location) {
            // TODO: Expansions: this filter should also consider a resource type, currently we don't care
            return $location instanceof Feature &&
                $location->getFeatureType() === FEATURE_STORE_RESOURCES &&
                $location->getResourcesAmount() < $location->getResourceLimit();
        })->first();
    }
}
