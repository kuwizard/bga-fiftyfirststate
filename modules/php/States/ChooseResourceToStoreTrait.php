<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Helpers\Collection;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\FeatureStorageMultiple;
use Bga\Games\Fiftyfirststate\Models\Player;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionMulti;

trait ChooseResourceToStoreTrait
{
    public function argChooseResourceToStore(): array
    {
        $args = [];
        $player = Players::getActive();
        $nonZeroResources = $this->getPlayersAvailableResources($player);
        $args[$player->getId()] = ResourcesHelper::getResourceNames($nonZeroResources);
        return ['_private' => $args];
    }

    public function actPassStoringResource()
    {
        Stack::insertOnTopAndFinish(ST_CONFIRM_TURN_END);
    }

    public function actChooseResourceToStore(string $resourceName): void
    {
        $player = Players::getActive();
        $resource = ResourcesHelper::getResourceType($resourceName);
        $player->decreaseResource($resource);
        $notificationData = [$resourceName => $player->getResource($resource)];
        Notifications::resourcesChanged($player, $notificationData);
        $location = $this->getFirstStorageLocationByResource($player, $resource);
        $location->addResource($resource);
        Notifications::resourcesPlacedOnLocation(
            $player,
            $location->getId(),
            ResourcesHelper::getResourceNames([$resource])
        );
        $nonFilledStorageLocations = $this->getAllNonFilledStorageLocations($player);
        if (!is_null($nonFilledStorageLocations)
            && !empty($this->getPlayersAvailableResources($player, $nonFilledStorageLocations))) {
            Stack::insertOnTop(ST_CHOOSE_RESOURCE_TO_STORE, ['pId' => $player->getId()]);
        } else {
            Stack::insertOnTop(ST_CONFIRM_TURN_END, ['pId' => $player->getId()]);
        }
        Stack::finishState();
    }

    /**
     * @return int[]
     */
    public function getPlayersAvailableResources(Player $player, Collection $nonFilledStorageLocations = null): array
    {
        $availableOptions = [];
        if (!$nonFilledStorageLocations) {
            $nonFilledStorageLocations = $this->getAllNonFilledStorageLocations($player);
        }
        /** @var FeatureStorageMultiple $location */
        foreach ($nonFilledStorageLocations as $location) {
            $availableOptions = array_merge($availableOptions, $location->getResourcesOptionsNotFilled());
        }

        $availableResources = [];
        foreach ($availableOptions as $option) {
            /** @var ResourceStorageOptionMulti $option */
            $availableResources = array_merge($availableResources, $option->getResources());
        }
        return array_values(array_filter(array_unique($availableResources), function ($resource) use ($player) {
            return $player->getResource($resource) > 0;
        }));
    }

    /**
     * @return Collection
     */
    public function getAllNonFilledStorageLocations(Player $player)
    {
        return $player->getBoard()->filter(function ($location) {
            return $location instanceof FeatureStorageMultiple &&
                !$location->isFullyFilled();
        });
    }

    /**
     * @param Player $player
     * @param int $resource
     * @return FeatureStorageMultiple
     */
    private function getFirstStorageLocationByResource(Player $player, int $resource)
    {
        return $player->getBoard()->filter(function ($location) use ($resource) {
            return $location instanceof FeatureStorageMultiple
                && !$location->isFullyFilled()
                && $location->isCanStoreResource($resource);
        })->first();
    }
}
