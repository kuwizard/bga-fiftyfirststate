<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Managers\Resources;
use STATE\Models\Feature;
use STATE\Models\Player;

trait ChooseResourceSourceTrait
{
    public function argChooseResourceSource()
    {
        $ctx = Stack::getCtx();
        $resource = $ctx['resourceIcon'];
        $toSpend = empty($ctx['sourcesRaw']) ? [$resource] : array_merge([$resource],
            array_merge(array_keys(...$ctx['sourcesRaw'])));
        $sources = $ctx['sources'];
        if (isset($sources['joker'])) {
            $sources['jokerIcon'] = ResourcesHelper::getResourceName($sources['joker']);
        }
        return [
            'resourceIcon' => ResourcesHelper::getResourceName($resource),
            'resourcesList' => ResourcesHelper::getResourceNames($toSpend),
            'sources' => $sources,
        ];
    }

    private function getPlayerLocationsWithResource($resource, $player)
    {
        $locations = Resources::getLocationIdsByResource($resource);
        $playerLocations = $player->getBoard()->getIds();
        return array_intersect($locations, $playerLocations);
    }

    public function stCreateResourceSourceMap()
    {
        $ctx = Stack::getCtx();
        $spend = $ctx['spend'];
        $player = Players::getActive();
        $sources = [];
        foreach ($spend as $resource) {
            $joker = Resources::getJokerFor($resource);
            $sourcesSingle = [
                'faction' => $player->getResource($resource) === 0 ? null : true,
                'locations' => $this->getPlayerLocationsWithResource($resource, $player),
                'joker' => is_null($joker) || $player->getResource($joker) === 0 ? null : $joker,
            ];
            foreach (array_keys($sourcesSingle) as $source) {
                if (!$sourcesSingle[$source]) {
                    unset($sourcesSingle[$source]);
                }
            }
            $sources[] = [$resource => $sourcesSingle];
        }
        Stack::insertOnTopAndFinish(ST_PROCESS_SOURCE_MAP, [
            'sourcesRaw' => $sources,
            'bonus' => $ctx['bonus'],
            'deploy' => $ctx['deploy'] ?? null,
            'activatorId' => $ctx['activatorId'] ?? null,
            'processed' => $ctx['processed'] ?? null,
        ]);
    }

    public function stProcessSourceMap()
    {
        $ctx = Stack::getCtx();
        $sourcesRaw = $ctx['sourcesRaw'] ?? null;
        $processed = $ctx['processed'] ?? [];
        while (count($sourcesRaw) > 0) {
            $sourceRaw = array_shift($sourcesRaw);
            $resource = array_key_first($sourceRaw);
            $sources = $sourceRaw[$resource];

            $onlyNotLocation = count($sources) === 1 && !isset($sources['locations']);
            $onlyLocation = count($sources) === 1 && isset($sources['locations']) && count($sources['locations']) === 1;
            if ($onlyNotLocation || $onlyLocation) {
                $processed[] = $resource;
                if (isset($sources['faction'])) {
                    $whereId = 0;
                } else {
                    $whereId = isset($sources['locations']) ? $sources['locations'][0] : $sources['joker'];
                }
                $this->decreaseResource($whereId, Players::getActive(), $resource, $ctx['activatorId']);
            } else {
                Stack::insertOnTopAndFinish(ST_CHOOSE_RESOURCE_SOURCE, [
                    'resourceIcon' => $resource,
                    'sources' => $sources,
                    'bonus' => $ctx['bonus'],
                    'deploy' => $ctx['deploy'],
                    'sourcesRaw' => $sourcesRaw,
                    'processed' => $processed,
                    'activatorId' => $ctx['activatorId'],
                ]);
                break;
            }
        }
        if (empty($sourcesRaw) && !Stack::isAtomIn(ST_CHOOSE_RESOURCE_SOURCE)) {
            $this->addBonus(Players::getActive());
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
        $this->decreaseResource(
            $id,
            Players::getActive(),
            in_array($id, ALL_RESOURCES_LIST) ? $id : $ctx['resourceIcon'],
            $ctx['activatorId']
        );

        Stack::insertOnTopAndFinish(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'bonus' => $ctx['bonus'],
            'deploy' => $ctx['deploy'],
            'spend' => empty($ctx['sourcesRaw']) ? [] : array_merge(array_keys(...$ctx['sourcesRaw'])),
            'processed' => array_merge($ctx['processed'], [$ctx['resourceIcon']]),
            'activatorId' => $ctx['activatorId'],
        ]);
    }

    /**
     * @param int $whereId
     * @param int $resource
     * @param Player $player
     * @return void
     */
    private function decreaseResource($whereId, $player, $resource, $activatorId)
    {
        if ($whereId === 0 || in_array($whereId, ALL_RESOURCES_LIST)) {
            $player->decreaseResource($resource);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames([$resource]));
        } else {
            Resources::delete($whereId, $resource);
            Notifications::resourcesLocationChanged($player, $whereId, ResourcesHelper::getResourceName($resource));
        }
        if ($activatorId >= FACTION_NEW_YORK && $activatorId <= FACTION_MERCHANTS + 3) {
            $actionId = $activatorId - $player->getFaction();
            // We need to show a token from requirements there, not ammo
            $actionChosen = $player->getFactionActions()[$actionId];
            Notifications::resourcesSpentFaction($player, [$actionChosen->getSpendRequirementsUIRemoveCard()[0]], $actionId);
        } else if (!is_null($activatorId)) {
            Notifications::resourcesPlacedOnLocation($player, $activatorId, [ResourcesHelper::getResourceName($resource)]);
        }
    }

    private function addBonus($player)
    {
        $ctx = Stack::getCtx();
        $resourcesChanged = [];
        if (isset($ctx['bonus']) && $ctx['bonus']) {
            foreach (array_count_values($ctx['bonus']) as $bonus => $amount) {
                $player->increaseResource($bonus, $amount);
                $resourcesChanged[] = $bonus;
                if ($bonus === RESOURCE_CARD) {
                    Notifications::handChanged($player);
                }
            }
        }
        if (isset($ctx['deploy'])) {
            $oldLocationId = $ctx['deploy']['old'];
            $newLocationId = $ctx['deploy']['new'];
            $oldLocation = Locations::get($oldLocationId);
            $newLocation = Locations::get($newLocationId);

            if ($oldLocation instanceof Feature && $oldLocation->getResourcesAmount() > 0) {
                $resourcesFromOldLocation = ResourcesHelper::increaseResourcesAfterAction(
                    $player,
                    $oldLocation->getResources()
                );
                Resources::deleteAll($oldLocation->getId());
                $resourcesChanged = array_merge($resourcesChanged, $resourcesFromOldLocation);
            }
            $player->discard($oldLocationId);
            Locations::move($newLocationId, [LOCATION_BOARD, $player->getId()]);
            Notifications::handChanged($player);
            Notifications::locationDiscarded($player, $oldLocationId, Locations::countInLocation(LOCATION_DISCARD));
            Notifications::locationBuilt($player, $newLocation, $newLocation->getFactionRow());
            $this->getProductionAfterBuildAndPlaceResources($newLocation, $player);
        }
        if (!empty($resourcesChanged)) {
            Notifications::resourcesChanged($player, $player->getResourcesWithNames(array_unique($resourcesChanged)));
        }
    }
}
