<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Core\Stats;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Managers\Resources;
use Bga\Games\Fiftyfirststate\Models\FeaturePassiveAbility;
use Bga\Games\Fiftyfirststate\Models\FeatureStorage;
use Bga\Games\Fiftyfirststate\Models\FeatureStorageSingle;
use Bga\Games\Fiftyfirststate\Models\Player;
use Bga\Games\Fiftyfirststate\Models\Production;

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
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
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
                    'joker' => is_null($joker) || $player->getResource($joker) === 0 ? null : $joker,
                    'locations' => $this->getPlayerLocationsWithResource($resource, $player),
                    'locationsWithJoker' => is_null($joker) ? null : $this->getPlayerLocationsWithResource($joker, $player),
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
            $onlyJokerOnLocation = count($sources) === 1 && isset($sources['locationsWithJoker']) && count(
                    $sources['locationsWithJoker']
                ) === 1;
            if ($onlyNotLocation || $onlyLocation || $onlyJokerOnLocation) {
                if (isset($sources['faction'])) {
                    $sourceId = 0;
                } else if (isset($sources['locations'])) {
                    $sourceId = $sources['locations'][0]->getId();
                } else if (isset($sources['joker'])) {
                    $sourceId = $sources['joker'];
                } else {
                    $sourceId = $sources['locationsWithJoker'][0]->getId();
                    $resource = Resources::getJokerFor($resource);
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
            $gotNewLocations = $this->postActions($player, $processed);
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
            if ($gotNewLocations || $this->gotNewLocations($ctx)) {
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

    private function gotNewLocations(array $ctx): bool
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

    /**
     * @throws \BgaVisibleSystemException
     */
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
            // Looks like $source is a card id
            $resourcesOnLocation = Resources::get($sourceId);
            if (!in_array($resource, $resourcesOnLocation)) {
                // Hmm, player must want to use a joker from this card
                $joker = Resources::getJokerFor($resource);
                if (!in_array($joker, $resourcesOnLocation)) {
                    throw new \BgaVisibleSystemException('Resource ' . $resource . ' not found on location ' . $sourceId);
                } else {
                    $resource = $joker;
                }
            }
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

    private function postActions(Player $player, array $processed): bool
    {
        $ctx = Stack::getCtx();
        $resourcesChanged = [];
        $gotNewLocations = false;
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
                    $gotNewLocations = $this->getProductionAfterBuildAndPlaceResources($location, $player);
                    $resourcesChanged[] = RESOURCE_CARD;
                    Stats::incPlayer($player, STAT_LOCATIONS_DEVELOPED);
                    break;
                case LOCATION_ACTION_RAZE:
                    Locations::move($location->getId(), LOCATION_DISCARD);
                    Notifications::locationRazed($player, $location, $processed);
                    $gotNewLocations = in_array(RESOURCE_CARD, $location->getSpoils());
                    $resourcesChanged[] = RESOURCE_CARD;
                    Stats::incPlayer($player, STAT_LOCATIONS_RAZED_FROM_HAND);
                    break;
                case LOCATION_ACTION_BUILD:
                    Notifications::locationBuilt($player, $location, $processed);
                    Locations::move($location->getId(), [LOCATION_BOARD, $player->getId()]);
                    $gotNewLocations = $this->getProductionAfterBuildAndPlaceResources($location, $player);
                    $resourcesChanged[] = RESOURCE_CARD;
                    Stats::locationBuilt($player, $location);
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
                    $gotNewLocations = in_array(RESOURCE_CARD, $location->getDeals());
                    $resourcesChanged[] = RESOURCE_CARD;
                    Stats::incPlayer($player, STAT_LOCATIONS_DEAL);
                    break;
                case LOCATION_ACTION_RAZE_OTHER:
                    $owner = Players::getOwner($location->getId());
                    $ownerResourcesChanged = ResourcesHelper::increaseResourcesAfterAction($owner, $location->getDeals());
                    Notifications::resourcesChanged($owner, $owner->getResourcesWithNames($ownerResourcesChanged));
                    $location->ruin();
                    Locations::resetActivatedTimes([$location->getId()]);
                    $gotNewLocations = in_array(RESOURCE_CARD, $location->getSpoils());
                    Notifications::locationRuined($owner, $location, $player, $location->getDefenceValue());
                    Stats::incPlayer($player, STAT_LOCATIONS_RAZED_OPPONENTS);
                    Stats::incPlayer($owner, STAT_PLAYER_VICTIM_OF_RAZE);
                    break;
                default:
                    throw new \BgaVisibleSystemException('Unknown action ' . $type);
            }
            /** @var FeaturePassiveAbility $location */
            foreach ($player->getAllPassiveLocations() as $location) {
                if ($type === LOCATION_ACTION_RAZE_OTHER) {
                    $type = LOCATION_ACTION_RAZE;
                }
                $resourcesAdded = $location->activatePassiveAbility($player, $type);
                $resourcesChanged = array_merge($resourcesChanged, $resourcesAdded);
                if (in_array(RESOURCE_CARD, $resourcesAdded)) {
                    $gotNewLocations = true;
                }
            }
        }
        if (isset($ctx['bonus']) && $ctx['bonus']) {
            foreach (array_count_values($ctx['bonus']) as $bonus => $amount) {
                $player->increaseResource($bonus, $amount);
                $resourcesChanged[] = $bonus;
                if ($bonus === RESOURCE_CARD) {
                    $gotNewLocations = true;
                }
            }
        }
        if (isset($ctx['activatorId']) && $ctx['activatorId'] < FACTION_NEW_YORK) {
            Locations::get($ctx['activatorId'])->postActivation();
        }
        if (!empty($resourcesChanged)) {
            if ($gotNewLocations) {
                Notifications::locationsDrawn($player);
                Notifications::deckChanged();
            }
            Notifications::resourcesChanged($player, $player->getResourcesWithNames(array_unique($resourcesChanged)));
        }
        if (Globals::getLastRoundNotify()) {
            Globals::setLastRoundNotify(false);
            Notifications::lastRound($player);
        }

        return $gotNewLocations;
    }

    public function getProductionAfterBuildAndPlaceResources($location, $player): bool
    {
        $resourcesChanged = [];
        // Gain resources (production or building bonus)
        if ($location instanceof Production || !empty($location->getBuildingBonus($player))) {
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
            $location->placeResources();
            Notifications::resourcesPlacedOnLocation(
                $player,
                $location->getId(),
                $location->getResourcesUI(),
                $location
            );
        }
        return in_array(RESOURCE_CARD, $resourcesChanged);
    }
}
