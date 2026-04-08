<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Helpers\Collection;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\Location;

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
        $isVoidIconOnBoard = !$board->filter(function (Location $location) {
            return in_array(ICON_VOID, $location->getIcons());
        })->empty();
        if ($resource === RESOURCE_DEVELOPMENT || $isRuins || $isVoidIconOnBoard) {
            $possibleHandLocations = $player->getHand();
        } else {
            $possibleHandLocations = $player->getHand()->filter(function (Location $location) use ($board) {
                $iconsOnBoard = array_merge(
                    ...$board->map(function (Location $location) {
                    return $location->getIcons();
                })->toArray()
                );
                $locationIsVoid = in_array(ICON_VOID, $location->getIcons());
                return !empty(array_intersect($location->getIcons(), $iconsOnBoard)) || $locationIsVoid;
            });
        }
        return $possibleHandLocations;
    }

    public function argDevelopChooseDestination()
    {
        $locationToBuildId = Stack::getCtx()['newLocationId'];
        $locationToBuild = Locations::get($locationToBuildId);
        $player = Players::getActive();
        $isToBuildVoid = in_array(ICON_VOID, $locationToBuild->getIcons());
        if (ResourcesHelper::getResourceType(Stack::getCtx()['resource']) === RESOURCE_DEVELOPMENT || $isToBuildVoid) {
            $possibleDestinations = $player->getBoard(true);
        } else {
            $possibleDestinations = $player->getBoard(true)->filter(
                function (Location $location) use ($locationToBuild) {
                    return $location->isRuined()
                        || in_array(ICON_VOID, $location->getIcons())
                        || !empty(array_intersect($location->getIcons(), $locationToBuild->getIcons()));
                }
            );
        }
        $cardWarning = in_array(RESOURCE_CARD, $locationToBuild->getBuildingBonus($player));
        return [
            'newLocationId' => $locationToBuildId,
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
