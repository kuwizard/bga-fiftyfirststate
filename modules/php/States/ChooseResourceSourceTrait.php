<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Players;
use STATE\Managers\Resources;

trait ChooseResourceSourceTrait
{
    public function argChooseResourceSource()
    {
        $resource = Stack::getCtx()['spend'][0];
        $player = Players::getActive();
        return [
            'faction' => $player->getResource($resource, true) === 0 ? null : ResourcesHelper::getResourceName(
                $resource
            ),
            'locations' => $this->getPlayerLocationsWithResource($resource, $player),
        ];
    }

    private function getPlayerLocationsWithResource($resource, $player)
    {
        $locations = Resources::getLocationIdsByResource($resource);
        $playerLocations = $player->getBoard()->getIds();
        return array_intersect($locations, $playerLocations);
    }

    public function stChooseSource()
    {
        $spend = Stack::getCtx()['spend'];
        $player = Players::getActive();
        $isResourcesOnLocations = false;
        foreach (array_keys(array_count_values($spend)) as $resource) {
            if ($this->getPlayerLocationsWithResource($resource, $player) !== []) {
                $isResourcesOnLocations = true;
            }
        }
        if (!$isResourcesOnLocations) {
            $resourcesChanged = [];
            foreach (array_count_values($spend) as $spendRequirement => $amount) {
                $player->decreaseResource($spendRequirement, $amount);
                $resourcesChanged[] = $spendRequirement;
            }
            Notifications::resourcesChanged($player, $player->getResourcesWithNames($resourcesChanged));
            Stack::finishState();
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function actChooseSource($id)
    {
        self::checkAction('actChooseSource');
        $spend = Stack::getCtx()['spend'];
        $resource = array_shift($spend);
        $player = Players::getActive();
        $resourceName = ResourcesHelper::getResourceName($resource);
        if ($id === 0) {
            $newAmount = $player->decreaseResource($resource);
            Notifications::resourcesChanged($player, [$resourceName => $newAmount]);
        } else {
            Resources::delete($id);
            Notifications::resourcesLocationChanged($player, $id, $resourceName);
        }
        if (count($spend) > 0) {
            Stack::insertOnTopAndFinish(ST_CHOOSE_RESOURCE_SOURCE, [
                'spend' => $spend,
            ]);
        } else {
            Stack::finishState();
        }
    }
}
