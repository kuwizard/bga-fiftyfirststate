<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Player;

trait SpecificLocationsActionsTrait
{
    public function argChooseResourceToSpend()
    {
        $player = Players::getActive();
        $filteredResources = array_filter(Stack::getCtx()['resources'], function ($resource) use ($player) {
            return $player->getResource($resource) > 0;
        });
        return ResourcesHelper::getResourceNames(array_values($filteredResources));
    }

    public function argChooseDealToLose()
    {
        $resourcesOnDeals = Players::getActive()->getDeals();
        return ResourcesHelper::getResourceNames(array_values(array_unique($resourcesOnDeals)));
    }

    public function actChooseDeal(string $resourceName)
    {
        $player = Players::getActive();
        $discarded = Locations::discardByDeal(ResourcesHelper::getResourceType($resourceName), $player->getId());
        Notifications::dealDiscarded($player, $discarded, Locations::countInLocation(LOCATION_DISCARD), $resourceName);
        $this->addAtomToContinueProcessResources(Stack::getCtx(), [$discarded]);
    }

    public function actChooseResourceToSpend(string $resource)
    {
        $resourceType = ResourcesHelper::getResourceType($resource);
        $this->decreaseResource($resourceType, Players::getActive(), $resourceType, Stack::getCtx()['activatorId']);
        $this->addAtomToContinueProcessResources(Stack::getCtx(), [$resourceType]);
    }
}
