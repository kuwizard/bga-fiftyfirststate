<?php

namespace STATE\States;

use STATE\Core\Stack;
use STATE\Helpers\Collection;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Location;

trait DeployTrait
{
    public function argDeployChooseFromHand()
    {
        return ['possibleHandIds' => $this->getLocationsAvailableToDeploy(Stack::getCtx()['resource'])->getIds()];
    }

    /**
     * @return Collection
     */
    public function getLocationsAvailableToDeploy($resource)
    {
        $player = Players::getActive();
        $board = $player->getBoard();
        $isRuins = !$board->filter(function (Location $location) {
            return $location->isRuined();
        })->empty();
        if ($resource === RESOURCE_DEVELOPMENT || $isRuins) {
            $possibleHandLocations = $player->getHand();
        } else {
            $possibleHandLocations = $player->getHand()->filter(function (Location $location) use ($board) {
                $iconsOnBoard = array_merge(
                    ...$board->map(function (Location $location) {
                    return $location->getIcons();
                })->toArray()
                );
                return !empty(array_intersect($location->getIcons(), $iconsOnBoard));
            });
        }
        return $possibleHandLocations;
    }

    public function argDeployChooseDestination()
    {
        $newLocationId = Stack::getCtx()['newLocationId'];
        $fromLocation = Locations::get($newLocationId);
        $player = Players::getActive();
        if (ResourcesHelper::getResourceType(Stack::getCtx()['resource']) === RESOURCE_BRICK) {
            $possibleDestinations = $player->getBoard()->filter(
                function (Location $location) use ($fromLocation) {
                    return $location->isRuined() || !empty(array_intersect($location->getIcons(), $fromLocation->getIcons()));
                }
            );
        } else {
            $possibleDestinations = $player->getBoard();
        }
        return [
            'newLocationId' => $newLocationId,
            'possibleDestinationIds' => $possibleDestinations->getIds(),
            'cardWarning' => in_array(RESOURCE_CARD, $fromLocation->getBuildingBonus($player)),
        ];
    }

    /**
     * @param int $id
     * @return void
     */
    public function actDeployChooseFromHand($id)
    {
        self::checkAction('actDeployChooseFromHand');
        Stack::insertOnTopAndFinish(
            ST_DEPLOY_CHOOSE_DESTINATION,
            ['resource' => Stack::getCtx()['resource'], 'newLocationId' => $id]
        );
    }

    /**
     * @param int $id
     * @return void
     */
    public function actDeployChooseDestination($id)
    {
        self::checkAction('actDeployChooseDestination');
        $resource = ResourcesHelper::getResourceType(Stack::getCtx()['resource']);
        if (!in_array($resource, [RESOURCE_BRICK, RESOURCE_DEVELOPMENT, RESOURCE_AMMO])) {
            throw new \BgaVisibleSystemException('Unexpected resource while developing: ' . $resource);
        }
        Stack::insertOnTopAndFinish(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => [$resource],
            'bonus' => [RESOURCE_VP],
            'postActions' => [
                'type' => 'deploy',
                'old' => $id,
                'id' => Stack::getCtx()['newLocationId'],
                'resource' => $resource,
            ],
        ]);
    }
}
