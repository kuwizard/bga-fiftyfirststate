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
        if (Stack::getCtx()['resource'] === RESOURCE_BRICK) {
            $possibleDestinations = Players::getActive()->getBoard()->filter(
                function (Location $location) use ($fromLocation) {
                    return $location->isRuined() || !empty(array_intersect($location->getIcons(), $fromLocation->getIcons()));
                }
            );
        } else {
            $possibleDestinations = Players::getActive()->getBoard();
        }
        return ['newLocationId' => $newLocationId, 'possibleDestinationIds' => $possibleDestinations->getIds()];
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
        if (!in_array($resource, [RESOURCE_BRICK, RESOURCE_DEVELOPMENT])) {
            throw new BgaVisibleSystemException('Unexpected resource while developing: ' . $resource);
        }
        Stack::insertOnTopAndFinish(ST_CHOOSE_RESOURCE_SOURCE, [
            'spend' => [$resource],
            'bonus' => [RESOURCE_VP],
            'deploy' => ['old' => $id, 'new' => Stack::getCtx()['newLocationId']],
        ]);
    }
}
