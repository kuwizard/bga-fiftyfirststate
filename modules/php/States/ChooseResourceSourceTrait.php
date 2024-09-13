<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Managers\Resources;
use STATE\Models\Feature;
use STATE\Models\FeatureStorageSingle;
use STATE\Models\Player;
use STATE\Models\Production;

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
            'spendText' => clienttranslate('Choose where to spend {resourceIcon} from:'),
            'sources' => $sources,
        ];
    }

    private function getPlayerLocationsWithResource($resource, $player)
    {
        $locations = Resources::getLocationIdsByResource($resource);
        $playerLocations = $player->getBoard()->getIds();
        return array_map(function ($locationId) {
            return Locations::get($locationId);
        }, array_intersect($locations, $playerLocations));
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
            'postActions' => $ctx['postActions'] ?? null,
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
                    $whereId = isset($sources['locations']) ? $sources['locations'][0]->getId() : $sources['joker'];
                }
                $this->decreaseResource($whereId, Players::getActive(), $resource, $ctx['activatorId']);
            } else {
                Stack::insertOnTopAndFinish(ST_CHOOSE_RESOURCE_SOURCE, [
                    'resourceIcon' => $resource,
                    'sources' => $sources,
                    'bonus' => $ctx['bonus'],
                    'postActions' => $ctx['postActions'],
                    'sourcesRaw' => $sourcesRaw,
                    'processed' => $processed,
                    'activatorId' => $ctx['activatorId'],
                ]);
                break;
            }
        }
        if (empty($sourcesRaw) && !Stack::isAtomIn(ST_CHOOSE_RESOURCE_SOURCE)) {
            $this->postActions(Players::getActive());
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
            'postActions' => $ctx['postActions'],
            'spend' => empty($ctx['sourcesRaw']) ? [] : array_merge(array_keys(...$ctx['sourcesRaw'])),
            'processed' => array_merge($ctx['processed'], [$ctx['resourceIcon']]),
            'activatorId' => $ctx['activatorId'],
        ]);
    }

    private function decreaseResource(int $whereId, Player $player, int $resource, int|null $activatorId): void
    {
        if (in_array($whereId, ALL_RESOURCES_LIST)) {
            $player->decreaseResource($whereId);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames([$whereId]));
        } else if ($whereId === 0) {
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

    private function postActions(Player $player)
    {
        $ctx = Stack::getCtx();
        $resourcesChanged = [];
        if (isset($ctx['bonus']) && $ctx['bonus']) {
            foreach (array_count_values($ctx['bonus']) as $bonus => $amount) {
                $player->increaseResource($bonus, $amount);
                $resourcesChanged[] = $bonus;
                if ($bonus === RESOURCE_CARD) {
                    Notifications::handChanged($player);
                    Notifications::deckChanged();
                }
            }
        }
        if (isset($ctx['postActions'])) {
            $type = $ctx['postActions']['type'];
            $locationId = $ctx['postActions']['id'];
            $location = Locations::get($locationId);
            if ($type === 'deploy') {
                $oldLocationId = $ctx['postActions']['old'];
                $oldLocation = Locations::get($oldLocationId);

                if ($oldLocation instanceof Feature && $oldLocation->getResourcesAmount() > 0) {
                    $resourcesFromOldLocation = ResourcesHelper::increaseResourcesAfterAction(
                        $player,
                        $oldLocation->getResources()
                    );
                    Resources::deleteAll($oldLocation->getId());
                    $resourcesChanged = array_merge($resourcesChanged, $resourcesFromOldLocation);
                }
                $player->discard($oldLocationId);
                Locations::move($locationId, [LOCATION_BOARD, $player->getId()]);
                Notifications::handChanged($player);
                Notifications::deckChanged();
                Notifications::locationDiscarded($player, $oldLocation);
                Notifications::locationBuilt($player, $location, $location->getFactionRow());
                $this->getProductionAfterBuildAndPlaceResources($location, $player);
            } elseif ($type === 'raze') {
                Notifications::handChanged($player);
                Notifications::deckChanged();
                Locations::move($location->getId(), LOCATION_DISCARD);
                Notifications::locationDiscarded($player, $location);
            } elseif ($type === 'build') {
                Notifications::locationBuilt($player, $location, $location->getFactionRow());
                Locations::move($location->getId(), [LOCATION_BOARD, $player->getId()]);
                $this->getProductionAfterBuildAndPlaceResources($location, $player);
            } elseif ($type === 'deal') {
                if (count($location->getDeals()) > 1) {
                    throw new BgaVisibleSystemException('More than 1 resource in deals, that should be impossible');
                }
                Locations::move($location->getId(), [LOCATION_DEALS, $player->getId()]);
                Notifications::locationDealMade(
                    $player,
                    $locationId,
                    ResourcesHelper::getResourceName($location->getDeals()[0])
                );
            }
            $resourcesChanged[] = RESOURCE_CARD;
        }
        if (isset($ctx['activatorId']) && $ctx['activatorId'] < FACTION_NEW_YORK) {
            Locations::get($ctx['activatorId'])->postActivation();
        }
        if (!empty($resourcesChanged)) {
            if (in_array(RESOURCE_CARD, $resourcesChanged)) {
                Notifications::handChanged($player);
            }
            Notifications::resourcesChanged($player, $player->getResourcesWithNames(array_unique($resourcesChanged)));
        }
    }

    public function getProductionAfterBuildAndPlaceResources($location, $player)
    {
        // Gain resources (production or building bonus)
        if ($location instanceof Production || !empty($location->getBuildingBonus($player))) {
            $resourcesChanged = [];
            if ($location instanceof Production) {
                $resourcesChanged = ResourcesHelper::increaseResourcesAfterAction(
                    $player,
                    $location->getProduct($player)
                );
            }
            if (!empty($location->getBuildingBonus($player))) {
                $resourcesChangedAgain = ResourcesHelper::increaseResourcesAfterAction(
                    $player,
                    $location->getBuildingBonus($player)
                );
                $resourcesChanged = array_unique(array_merge($resourcesChanged, $resourcesChangedAgain));
            }
            Notifications::resourcesChanged($player, $player->getResourcesWithNames($resourcesChanged));
        }
        // Place resources on a card
        if ($location instanceof FeatureStorageSingle) {
            $location->placeResourcesOneType($location->getResourceType(), $location->getResourceLimit());
            Notifications::resourcesPlacedOnLocation(
                $player,
                $location->getId(),
                $location->getResourcesUI()
            );
        }
    }
}
