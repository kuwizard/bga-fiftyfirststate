<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Managers\Resources;
use STATE\Models\FeatureStorage;
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
            array_map('key', $ctx['sourcesRaw']));
        $sources = $ctx['sources'];
        if (isset($sources['joker'])) {
            $sources['jokerIcon'] = ResourcesHelper::getResourceName($sources['joker']);
        }
        // TODO: Remove this "if" after 24/11/2024
        if (isset($sources['locations'])) {
            $sources['locations'] = array_values($sources['locations']);
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
        return array_values(array_map(function ($locationId) {
            return Locations::get($locationId);
        }, array_intersect($locations, $playerLocations)));
    }

    public function stCreateResourceSourceMap()
    {
        $ctx = Stack::getCtx();
        $spend = $ctx['spend'];
        $player = Players::getActive();
        $sources = [];
        foreach ($spend as $resource) {
            if ($resource === RESOURCE_DEAL || $resource === RESOURCE_CARD) {
                $sources[] = [$resource => ['faction' => true]];
            } else {
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
        }
        Stack::insertOnTopAndFinish(
            ST_PROCESS_SOURCE_MAP,
            $this->getCommonChooseResourceData($ctx, $sources, $ctx['processed'] ?? null)
        );
    }

    public function stProcessSourceMap()
    {
        $ctx = Stack::getCtx();
        $sourcesRaw = $ctx['sourcesRaw'] ?? null;
        $processed = $ctx['processed'] ?? [];
        $player = Players::getActive();
        while (count($sourcesRaw) > 0) {
            $sourceRaw = array_shift($sourcesRaw);
            $resource = array_key_first($sourceRaw);
            $sources = $sourceRaw[$resource];

            $onlyNotLocation = count($sources) === 1 && !isset($sources['locations']);
            $onlyLocation = count($sources) === 1 && isset($sources['locations']) && count($sources['locations']) === 1;
            if ($onlyNotLocation || $onlyLocation) {
                if (isset($sources['faction'])) {
                    $sourceId = 0;
                } else {
                    $sourceId = isset($sources['locations']) ? $sources['locations'][0]->getId() : $sources['joker'];
                }
                if (!in_array($resource, [RESOURCE_DEAL, RESOURCE_ANY_OF_MAIN])) {
                    $processed[] = $sources['joker'] ?? $resource;
                }
                if ($resource === RESOURCE_DEAL) {
                    Stack::insertOnTopAndFinish(
                        ST_CHOOSE_DEAL_TO_LOSE,
                        $this->getCommonChooseResourceData($ctx, $sourcesRaw, $processed)
                    );
                    break;
                } else if ($resource === RESOURCE_ANY_OF_MAIN) {
                    Stack::insertOnTopAndFinish(
                        ST_CHOOSE_RESOURCE_TO_SPEND,
                        [
                            'resources' => MAIN_RESOURCES_LIST,
                            ...$this->getCommonChooseResourceData($ctx, $sourcesRaw, $processed),
                        ]
                    );
                    break;
                } else if ($resource === RESOURCE_CARD) {
                    Stack::insertOnTopAndFinish(
                        ST_DISCARD_LOCATION_FOR_RESOURCES,
                        $this->getCommonChooseResourceData($ctx, $sourcesRaw, $processed)
                    );
                    break;
                } else {
                    $this->decreaseResource($sourceId, $player, $resource, $ctx['activatorId']);
                }
            } else {
                Stack::insertOnTopAndFinish(ST_CHOOSE_RESOURCE_SOURCE, [
                    'resourceIcon' => $resource,
                    'sources' => $sources,
                    ...$this->getCommonChooseResourceData($ctx, $sourcesRaw, $processed),
                ]);
                break;
            }
        }
        if (empty($sourcesRaw)
            && !Stack::isSomeAtomsIn(
                [
                    ST_CHOOSE_RESOURCE_SOURCE,
                    ST_CHOOSE_DEAL_TO_LOSE,
                    ST_CHOOSE_RESOURCE_TO_SPEND,
                    ST_DISCARD_LOCATION_FOR_RESOURCES,
                ]
            )) {
            $this->postActions($player, $processed);
            if ($ctx['activatorId']) {
                if ($ctx['activatorId'] < FACTION_NEW_YORK) {
                    $owner = Players::getOwner($ctx['activatorId']);
                    if ($owner->getId() !== $player->getId()) {
                        $victim = $owner;
                    }
                }
                Notifications::actionUsed(
                    $player,
                    $ctx['activatorId'],
                    $processed,
                    $ctx['bonus'],
                    $victim ?? null,
                    $ctx['isDeal'] ?? null
                );
            }
            if ($this->isGoingToGetANewLocation($ctx)) {
                $this->undoSavepoint();
            } else {
                if (Stack::isSomeAtomsIn([ST_ACTIVATE_SECOND_TIME, ST_ACTIVATE_SPEND_WORKERS_AGAIN])) {
                    Globals::setAddConfirmTurnEnd(true);
                } else {
                    Stack::insertOnTop(ST_CONFIRM_TURN_END);
                }
            }
            Stack::finishState();
        }
    }

    private function getCommonChooseResourceData(array $ctx, array $sourcesRaw, array|null $processed): array
    {
        return [
            'bonus' => $ctx['bonus'],
            'postActions' => $ctx['postActions'] ?? null,
            'sourcesRaw' => $sourcesRaw,
            'processed' => $processed,
            'activatorId' => $ctx['activatorId'] ?? null,
            'isDeal' => $ctx['isDeal'] ?? null,
        ];
    }

    private function isGoingToGetANewLocation(array $ctx): bool
    {
        $location = isset($ctx['postActions']['id']) ? Locations::get($ctx['postActions']['id']) : null;
        return (isset($ctx['postActions']['type'])
                && in_array($ctx['postActions']['type'], [LOCATION_ACTION_BUILD, LOCATION_ACTION_DEVELOP])
                && $location
                && in_array(RESOURCE_CARD, $location->getBuildingBonus(Players::getActive())))
            || in_array(RESOURCE_CARD, $ctx['bonus']);
    }

    public function actChooseSource(int $id): void
    {
        self::checkAction('actChooseSource');
        $ctx = Stack::getCtx();
        $resourceToSpend = in_array($id, ALL_RESOURCES_LIST) ? $id : $ctx['resourceIcon'];
        $this->decreaseResource(
            $id,
            Players::getActive(),
            $resourceToSpend,
            $ctx['activatorId']
        );
        $this->addAtomToContinueProcessResources($ctx, [$resourceToSpend]);
    }

    public function addAtomToContinueProcessResources(array $ctx, array $newResource, array $additionalData = []): void
    {
        Stack::insertOnTopAndFinish(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'bonus' => $ctx['bonus'],
            'postActions' => $ctx['postActions'],
            'spend' => empty($ctx['sourcesRaw']) ? [] : array_map('key', $ctx['sourcesRaw']),
            'processed' => array_merge($ctx['processed'], $newResource),
            'activatorId' => $ctx['activatorId'],
            ...$additionalData,
        ]);
    }

    // TODO: refactor this to mitigate difference between $sourceId and $resource as they seem to intersect thus confusing
    private function decreaseResource(int $sourceId, Player $player, int $resource, int|null $activatorId): void
    {
        if (in_array($sourceId, ALL_RESOURCES_LIST)) {
            $resource = $sourceId;
            $player->decreaseResource($sourceId);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames([$sourceId]));
        } else if ($sourceId === 0) {
            $player->decreaseResource($resource);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames([$resource]));
        } else {
            Resources::delete($sourceId, $resource);
            Notifications::resourcesLocationChanged($player, $sourceId, ResourcesHelper::getResourceName($resource));
        }
        if (!is_null($activatorId)) {
            if ($this->isFactionAction($activatorId)) {
                $actionId = $activatorId - $player->getFaction();
                // We need to show a token from requirements there, not ammo
                $actionChosen = $player->getFactionActions()[$actionId];
                Notifications::resourcesSpentFaction($player, [$actionChosen->getSpendRequirementsUIRemoveCard()[0]], $actionId);
            } else if (!$this->isWorkersAction($activatorId)) {
                Notifications::resourcesPlacedOnLocation($player, $activatorId, [ResourcesHelper::getResourceName($resource)]);
            }
        }
    }

    private function isFactionAction(int $activatorId): bool
    {
        return $activatorId >= FACTION_NEW_YORK && $activatorId <= FACTION_MERCHANTS + 2 && $activatorId % 10 <= 2;
    }

    private function isWorkersAction(int $activatorId): bool
    {
        return $activatorId >= FACTION_NEW_YORK && $activatorId <= FACTION_MERCHANTS + 3 && $activatorId % 10 === 3;
    }

    private function postActions(Player $player, array $processed)
    {
        $ctx = Stack::getCtx();
        $resourcesChanged = [];
        if (isset($ctx['postActions'])) {
            $type = $ctx['postActions']['type'];
            $locationId = $ctx['postActions']['id'];
            $location = Locations::get($locationId);
            switch ($type) {
                case LOCATION_ACTION_DEVELOP:
                    $oldLocationId = $ctx['postActions']['old'];
                    $oldLocation = Locations::get($oldLocationId);
                    $resourcesPlaced = $oldLocation instanceof FeatureStorage && $oldLocation->getResourcesAmount() > 0;
                    if ($resourcesPlaced) {
                        $resourcesFromOldLocation = ResourcesHelper::increaseResourcesAfterAction(
                            $player,
                            $oldLocation->getResources()
                        );
                        Resources::deleteAll($oldLocation->getId());
                        $resourcesChanged = array_merge($resourcesChanged, $resourcesFromOldLocation);
                    }
                    $player->discardSingle($oldLocationId);
                    Locations::move($locationId, [LOCATION_BOARD, $player->getId()]);
                    $oldLocation->unruin();
                    Locations::resetActivatedTimes([$oldLocation->getId()]);
                    Notifications::locationDiscarded($player, $oldLocation, $resourcesPlaced);
                    Notifications::locationBuilt($player, $location, $processed, $ctx['postActions']['resource'], $oldLocation);
                    $this->getProductionAfterBuildAndPlaceResources($location, $player);
                    $resourcesChanged[] = RESOURCE_CARD;
                    break;
                case LOCATION_ACTION_RAZE:
                    Locations::move($location->getId(), LOCATION_DISCARD);
                    Notifications::locationRazed($player, $location, $processed);
                    $resourcesChanged[] = RESOURCE_CARD;
                    break;
                case LOCATION_ACTION_BUILD:
                    Notifications::locationBuilt($player, $location, $processed);
                    Locations::move($location->getId(), [LOCATION_BOARD, $player->getId()]);
                    $this->getProductionAfterBuildAndPlaceResources($location, $player);
                    $resourcesChanged[] = RESOURCE_CARD;
                    break;
                case LOCATION_ACTION_DEAL:
                    if (count($location->getDeals()) > 1) {
                        throw new \BgaVisibleSystemException('More than 1 resource in deals, that should be impossible');
                    }
                    Locations::move($location->getId(), [LOCATION_DEALS, $player->getId()]);
                    Notifications::locationDealMade(
                        $player,
                        $location,
                        $processed
                    );
                    $resourcesChanged[] = RESOURCE_CARD;
                    break;
                case LOCATION_ACTION_RAZE_OTHER:
                    $owner = Players::getOwner($location->getId());
                    $ownerResourcesChanged = ResourcesHelper::increaseResourcesAfterAction($owner, $location->getDeals());
                    Notifications::resourcesChanged($owner, $owner->getResourcesWithNames($ownerResourcesChanged));
                    $location->ruin();
                    Locations::resetActivatedTimes([$location->getId()]);
                    Notifications::locationRuined($owner, $location, $player, $location->getDefenceValue());
                    break;
                default:
                    throw new \BgaVisibleSystemException('Unknown action ' . $type);
            }
        }
        if (isset($ctx['bonus']) && $ctx['bonus']) {
            foreach (array_count_values($ctx['bonus']) as $bonus => $amount) {
                $player->increaseResource($bonus, $amount);
                $resourcesChanged[] = $bonus;
            }
        }
        if (isset($ctx['activatorId']) && $ctx['activatorId'] < FACTION_NEW_YORK) {
            Locations::get($ctx['activatorId'])->postActivation();
        }
        if (!empty($resourcesChanged)) {
            if (in_array(RESOURCE_CARD, $resourcesChanged)) {
                Notifications::locationsDrawn($player);
                Notifications::deckChanged();
            }
            Notifications::resourcesChanged($player, $player->getResourcesWithNames(array_unique($resourcesChanged)));
        }
        if (Globals::getLastRoundNotify()) {
            Globals::setLastRoundNotify(false);
            Notifications::lastRound($player);
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
            Notifications::gotProductionAndOrBuildingBonuses(
                $player,
                $location,
                $location instanceof Production ? $location->getProduct($player) : [],
                $location->getBuildingBonus($player)
            );
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
                $location->getResourcesUI(),
                $location
            );
        }
    }
}
