<?php

namespace STATE\States;

use STATE\Core\Stack;
use STATE\Helpers\Collection;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Location;

trait DevelopTrait
{
    public function argDevelopChooseFromHand()
    {
        $resource = ResourcesHelper::getResourceType(Stack::getCtx()['resource']);
        $availableLocationIds = $this->getLocationsAvailableToDevelop($resource)->getIds();
        return ['possibleHandIds' => $this->getMapWithCardConfirmation($availableLocationIds, false)];
    }

    /**
     * @return Collection
     */
    public function getLocationsAvailableToDevelop(int $resource)
    {
        $player = Players::getActive();
        $board = $player->getBoard(true);
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

    public function argDevelopChooseDestination()
    {
        $newLocationId = Stack::getCtx()['newLocationId'];
        $fromLocation = Locations::get($newLocationId);
        $player = Players::getActive();
        if (ResourcesHelper::getResourceType(Stack::getCtx()['resource']) === RESOURCE_DEVELOPMENT) {
            $possibleDestinations = $player->getBoard(true);
        } else {
            $possibleDestinations = $player->getBoard(true)->filter(
                function (Location $location) use ($fromLocation) {
                    return $location->isRuined() || !empty(array_intersect($location->getIcons(), $fromLocation->getIcons()));
                }
            );
        }
        $cardWarning = in_array(RESOURCE_CARD, $fromLocation->getBuildingBonus($player));
        return [
            'newLocationId' => $newLocationId,
            'possibleDestinations' => $this->getMapWithCardConfirmation($possibleDestinations->getIds(), $cardWarning),
        ];
    }

    private function getMapWithCardConfirmation(array $ids, bool $confirmation)
    {
        $idsMapped = [];
        foreach ($ids as $id) {
            $idsMapped[$id] = $confirmation;
        }
        return $idsMapped;
    }

    public function actDevelopChooseFromHand(int $id): void
    {
        Stack::insertOnTopAndFinish(
            ST_DEVELOP_CHOOSE_DESTINATION,
            ['resource' => Stack::getCtx()['resource'], 'newLocationId' => $id]
        );
    }

    public function actDevelopChooseDestination(int $id): void
    {
        $resource = ResourcesHelper::getResourceType(Stack::getCtx()['resource']);
        if (!in_array($resource, [RESOURCE_BRICK, RESOURCE_DEVELOPMENT, RESOURCE_AMMO])) {
            throw new \BgaVisibleSystemException('Unexpected resource while developing: ' . $resource);
        }
        Stack::insertOnTopAndFinish(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => [$resource],
            'bonus' => [RESOURCE_VP],
            'postActions' => [
                'type' => 'develop',
                'old' => $id,
                'id' => Stack::getCtx()['newLocationId'],
                'resource' => $resource,
            ],
        ]);
    }
}
