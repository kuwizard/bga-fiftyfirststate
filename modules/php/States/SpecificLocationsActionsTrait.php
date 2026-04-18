<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;

trait SpecificLocationsActionsTrait
{
    public function argChooseResourceToSpend()
    {
        $player = Players::getActive();
        $filteredResources = array_filter(Stack::getCtx()['resources'], function ($resource) use ($player) {
            return $player->getResource($resource, false) > 0;
        });
        return [
            'resources' => ResourcesHelper::getResourceNames(array_values($filteredResources)),
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
        ];
    }

    public function argChooseDealToLose()
    {
        $resourcesOnDeals = Players::getActive()->getDeals();
        return [
            'resources' => ResourcesHelper::getResourceNames(array_values(array_unique($resourcesOnDeals))),
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
        ];
    }

    public function actChooseDeal(string $resourceName): void
    {
        $player = Players::getActive();
        $discarded = Locations::discardByDeal(ResourcesHelper::getResourceType($resourceName), $player->getId());
        Notifications::dealDiscarded($player, $discarded, Locations::countInLocation(LOCATION_DISCARD), $resourceName);
        $this->addAtomToContinueProcessResources(Stack::getCtx(), [$discarded], ['isDeal' => true]);
    }

    public function actChooseResourceToSpend(string $resourceName): void
    {
        $resourceType = ResourcesHelper::getResourceType($resourceName);
        $ctx = Stack::getCtx();
        $spend = empty($ctx['sourcesRaw']) ? [] : array_map('key', $ctx['sourcesRaw']);
        $this->addAtomToContinueProcessResources($ctx, [], ['spend' => array_merge($spend, [$resourceType])]);
    }
}
