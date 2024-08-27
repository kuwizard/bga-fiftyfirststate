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
            'faction' => $player->getResource($resource) === 0 ? null : ResourcesHelper::getResourceName(
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
        $ctx = Stack::getCtx();
        $spend = $ctx['spend'];
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
            $this->addBonus($resourcesChanged, $player);
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
        $ctx = Stack::getCtx();
        $spend = $ctx['spend'];
        $resource = array_shift($spend);
        $player = Players::getActive();
        $resourceName = ResourcesHelper::getResourceName($resource);
        $resourcesChanged = [];
        if ($id === 0) {
            $player->decreaseResource($resource);
            $resourcesChanged[] = $resource;
        } else {
            Resources::delete($id, $resource);
            Notifications::resourcesLocationChanged($player, $id, $resourceName);
        }
        if (count($spend) > 0) {
            Stack::insertOnTopAndFinish(ST_CHOOSE_RESOURCE_SOURCE, [
                'spend' => $spend,
                'bonus' => $ctx['bonus'] ?? null,
            ]);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames($resourcesChanged));
        } else {
            $this->addBonus($resourcesChanged, $player);
            Stack::finishState();
        }
    }

    private function addBonus($resourcesChanged, $player)
    {
        $ctx = Stack::getCtx();
        if (isset($ctx['bonus']) && $ctx['bonus']) {
            foreach (array_count_values($ctx['bonus']) as $bonus => $amount) {
                $player->increaseResource($bonus, $amount);
                $resourcesChanged[] = $bonus;
            }
        }
        if (!empty($resourcesChanged)) {
            Notifications::resourcesChanged($player, $player->getResourcesWithNames($resourcesChanged));
        }
    }
}
