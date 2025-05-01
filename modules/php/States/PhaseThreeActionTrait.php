<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\Collection;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Connections;
use STATE\Managers\Factions;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Act;
use STATE\Models\Location;
use STATE\Models\Player;
use STATE\Models\Production;

trait PhaseThreeActionTrait
{
    public function argPhaseThreeAction()
    {
        $player = Players::getActive();
        $allOtherLocations = new Collection();
        /** @var Player $otherPlayer */
        foreach (Players::getAllNonPassed($player->getId()) as $otherPlayer) {
            $allOtherLocations = $allOtherLocations->merge($otherPlayer->getBoard());
        }
        $otherPlayersLocations = $allOtherLocations->filter(function ($location) use ($player) {
            $razeReachable = $location->getDefenceValue() <= $player->getResource(RESOURCE_ARROW_RED, false, true);
            return !$location->isRuined() && ($this->isLocationCouldBeUsedAsOpenProd($location, $player) || $razeReachable);
        });

        $otherPlayersResources = [];
        /** @var Player $otherPlayer */
        foreach (Players::getAllNonPassed($player->getId()) as $otherPlayer) {
            $boardLocations = $otherPlayer->getBoard();
            $allOtherLocations = $allOtherLocations->merge($boardLocations);
            $playersOpenResourcesArray = $boardLocations->map(function ($location) use ($player) {
                return $this->isLocationCouldBeUsedAsOpenProd($location, $player)
                    ? $location->getProduct($player) : null;
            });
            $playersOpenResources = [];
            foreach ($playersOpenResourcesArray->toArray() as $resources) {
                if (!is_null($resources)) {
                    $playersOpenResources = array_merge($playersOpenResources, $resources);
                }
            }
            $otherPlayersResources[$otherPlayer->getId()] = ResourcesHelper::getResourceNames(
                array_values(array_unique($playersOpenResources))
            );
        }

        $connectionsToTake = $player->getResource(RESOURCE_WORKER, false) >= 2 ? Connections::getBothAvailable()->getIds() : [];
        return [
            'spendWorkers' => $player->getResource(RESOURCE_WORKER, false) >= 2,
            'factionActions' => !empty($player->getAvailableFactionActions()),
            'locations' => $player->getPlayableLocationsWithCardWarnings(),
            'otherPlayersLocations' => $this->mapLocationsWithCardGetting($otherPlayersLocations->toArray(), $player),
            'otherPlayersResources' => $otherPlayersResources,
            'develop' => $this->whatCanBeUsedForDevel($player),
            'connectionsToTake' => $connectionsToTake,
            'connectionsToPlay' => $player->getPlayableConnectionsIds(),
            'placeDefence' => $player->getResource(RESOURCE_DEFENCE) >= 1,
        ];
    }

    private function mapLocationsWithCardGetting(array $locations, Player $player): array
    {
        $locationsMapped = [];
        foreach ($locations as $location) {
            $locationsMapped[$location->getId()] = (
                $this->isOpenProdProducingCards($location, $player)
                xor $this->razeReachableCardInSpoils($location, $player)
            );
        }
        return $locationsMapped;
    }

    private function isOpenProdProducingCards(Location $location, Player $player)
    {
        return $location instanceof Production && in_array(RESOURCE_CARD, $location->getProduct($player));
    }

    private function razeReachableCardInSpoils(Location $location, Player $player)
    {
        $reachableAndGivesACard = $location->getDistance() <= $player->getResource(RESOURCE_ARROW_RED)
            && in_array(RESOURCE_CARD, $location->getSpoils());
        $playerGetsCardOnRaze = $player->isReceiveNewCardOnRaze();
        return $reachableAndGivesACard || $playerGetsCardOnRaze;
    }

    private function whatCanBeUsedForDevel(Player $player): array
    {
        $locationsToDevelopWithBrick = $this->getLocationsAvailableToDevelop(RESOURCE_BRICK);
        return [
            'brick' => !$locationsToDevelopWithBrick->empty() && $player->getResource(RESOURCE_BRICK, false) >= 1,
            'development' => $player->getResource(RESOURCE_DEVELOPMENT, false) >= 1
                && !$this->getLocationsAvailableToDevelop(RESOURCE_DEVELOPMENT)->empty(),
            'ammo' => !$locationsToDevelopWithBrick->empty()
                && $player->getResource(RESOURCE_AMMO, false) >= 1
                && $player->getResource(RESOURCE_BRICK, false) === 0,
        ];
    }

    public function argFactionActions()
    {
        $actions = Players::getActive()->getAvailableFactionActions();
        $ctx = Stack::getCtx();
        $player = Players::getActive();
        if (isset($ctx['combined']) && $ctx['combined'] && $player->getResource(RESOURCE_WORKER, false) >= 2) {
            $spendWorkersAction = new Act([RESOURCE_WORKER, RESOURCE_WORKER], [RESOURCE_ANY_OF_MAIN_PLUS_CARD]);
            // 0, 1 and 2 might be faction actions, we don't want to conflict with them. Maybe new factions will have 3 or 4 as well...
            $actions[10] = $spendWorkersAction;
        }
        return $actions;
    }

    public function argLocationActions()
    {
        $location = $this->getLocationFromCtx();
        return [
            'id' => $location->getId(),
            'actions' => Players::getActive()->getAvailableLocationActions($location),
            'locationActionsLexemes' => [
                LOCATION_ACTION_RAZE => clienttranslate('Raze'),
                LOCATION_ACTION_DEAL => clienttranslate('Make a deal'),
                LOCATION_ACTION_BUILD => clienttranslate('Build'),
            ],
        ];
    }

    public function argOpenProductionOrRaze()
    {
        $location = $this->getLocationFromCtx();
        $player = Players::getActive();
        return [
            'locationId' => $location->getId(),
            'raze' => $this->razeReachableCardInSpoils($location, $player),
            'openProd' => $this->isOpenProdProducingCards($location, $player),
        ];
    }

    public function actActionPass(): void
    {
        $player = Players::getActive();
        $player->markAsPassed();
        Notifications::playerPassed($player);
        $nonFilledStorageLocations = $this->getAllNonFilledStorageLocations($player);
        if (!is_null($nonFilledStorageLocations)
            && !empty($this->getPlayersAvailableResources($player, $nonFilledStorageLocations))) {
            Stack::insertOnTop(ST_CHOOSE_RESOURCE_TO_STORE, ['pId' => $player->getId()]);
        }
        if (Players::isAllPassed()) {
            Stack::unsuspendNext(ST_PHASE_THREE_ACTION);
            // A hack to prevent last player passing twice after using Undo and having 2 identical atoms
            Stack::removeSecondStateIfExists(ST_PHASE_THREE_ACTION);
        }
        Stack::finishState();
    }

    public function actSpendWorkers(): void
    {
        if (Players::getActive()->getResource(RESOURCE_WORKER, false) > 3) {
            Stack::insertOnTop(ST_ACTIVATE_SPEND_WORKERS_AGAIN);
        }
        Stack::insertOnTopAndFinish(ST_SPEND_WORKERS);
    }

    public function actUndo(): void
    {
        Stack::removeAllAtomsWithState(ST_ACTIVATE_SECOND_TIME);
        Stack::removeAllAtomsWithState(ST_ACTIVATE_SPEND_WORKERS_AGAIN);
        Stack::insertOnTopAndFinish(ST_PHASE_THREE_ACTION);
    }

    public function actGainResourceForWorkers(string $resourceName): void
    {
        Stack::insertOnTopAndFinish(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => [RESOURCE_WORKER, RESOURCE_WORKER],
            'bonus' => [ResourcesHelper::getResourceType($resourceName)],
            // 3 is a magic number for spending workers. 0-2 are for faction actions
            'activatorId' => Players::getActive()->getFaction() + 3,
        ]);
    }

    public function actEnableFactionActions(bool $combined): void
    {
        Stack::insertOnTopAndFinish(ST_FACTION_ACTIONS, ['combined' => $combined]);
    }

    public function actEnablePlaceDefenceState()
    {
        Stack::insertOnTopAndFinish(ST_PLACE_DEFENCE);
    }

    public function actFactionAct(int $id): void
    {
        $player = Players::getActive();
        $availableActions = $player->getAvailableFactionActions();
        if (isset($availableActions[$id])) {
            /** @var Act $actionChosen */
            $actionChosen = $availableActions[$id];
            $actionChosen->activate($id + $player->getFaction());
            Factions::setAsUsed($player->getFaction(), $id);
            self::giveExtraTime($player->getId());
            Stack::finishState();
        } else {
            throw new \BgaVisibleSystemException(
                'You\'re trying to activate an unavailable action. Please create a bug in this state so developer can investigate what happened!'
            );
        }
    }

    public function actUseLocation(int $id): void
    {
        self::giveExtraTime(Players::getActiveId());
        Stack::insertOnTopAndFinish(ST_LOCATION_ACTIONS, ['locationId' => $id]);
    }

    public function actLocationBuild(): void
    {
        $location = $this->getLocationFromCtx();
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_GREY, LOCATION_ACTION_BUILD);
    }

    public function actLocationRaze(): void
    {
        $location = $this->getLocationFromCtx();
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_RED, LOCATION_ACTION_RAZE, 'getSpoils');
    }

    public function actLocationDeal(): void
    {
        $location = $this->getLocationFromCtx();
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_BLUE, LOCATION_ACTION_DEAL, 'getDeals');
    }

    private function getLocationFromCtx()
    {
        $locationId = Stack::getCtx()['locationId'];
        return Locations::get($locationId);
    }

    /**
     * @param Player $player
     * @param Location|null $location
     * @param int $decrease
     * @param string $actionType
     * @param string $increase
     * @return void
     */
    private function razeBuildDealCommon($location, $decrease, $actionType, $increase = null)
    {
        $bonus = $increase ? $location->{$increase}() : [];
        Stack::insertOnTop(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => array_fill(0, $location->getDistance(), $decrease),
            'bonus' => $bonus,
            'postActions' => ['type' => $actionType, 'id' => $location->getId()],
        ]);
        self::giveExtraTime(Players::getActiveId());
        Stack::finishState();
    }

    public function actDiscardLocation(int $id): void
    {
        $player = Players::getActive();
        $player->discardSingle($id);
        $location = Locations::get($id);
        Notifications::locationDiscarded($player, $location);
        self::giveExtraTime($player->getId());
        $this->addAtomToContinueProcessResources(Stack::getCtx(), [$location]);
    }

    public function actDiscardConnection(int $id): void
    {
        $player = Players::getActive();
        $player->discardConnection($id);
        Notifications::connectionDiscarded($player, $id);
        self::giveExtraTime($player->getId());
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
        $this->addAtomToContinueProcessResources(Stack::getCtx(), []);
    }

    public function actActivateLocation(int $id): void
    {
        $location = Locations::get($id);
        $player = Players::getActive();
        $location->activate($player);
        self::giveExtraTime($player->getId());
        Stack::finishState();
    }

    public function actUseOtherPlayerLocation(int $id): void
    {
        $location = Locations::get($id);
        $player = Players::getActive();
        $locationCouldBeUsedAsOpenProd = $this->isLocationCouldBeUsedAsOpenProd($location, $player);
        $couldBeRazed = $player->getResource(RESOURCE_ARROW_RED, false, true) >= $location->getDefenceValue();
        if ($locationCouldBeUsedAsOpenProd && $couldBeRazed) {
            Stack::insertOnTop(ST_OPEN_PRODUCTION_OR_RAZE, ['locationId' => $id]);
        } elseif ($locationCouldBeUsedAsOpenProd) {
            $location->activate($player);
        } elseif ($couldBeRazed) {
            $this->razeOtherPlayersLocation($location);
        } else {
            throw new \BgaVisibleSystemException(
                'Something went wrong during activating other player location. Is open production: ' . $locationCouldBeUsedAsOpenProd . ', could be razed: ' . $couldBeRazed
            );
        }
        Stack::finishState();
    }

    private function isLocationCouldBeUsedAsOpenProd($location, $player): bool
    {
        $isOpenProd = $location instanceof Production && $location->isOpen();
        $workersAmount = $player->getResource(RESOURCE_WORKER, false);
        return $isOpenProd && $location->isActivatable() && $workersAmount > 0;
    }

    private function razeOtherPlayersLocation(Location $location)
    {
        Stack::insertOnTop(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => array_fill(0, $location->getDefenceValue(), RESOURCE_ARROW_RED),
            'bonus' => $location->getSpoils(),
            'postActions' => ['type' => LOCATION_ACTION_RAZE_OTHER, 'id' => $location->getId()],
        ]);
    }

    public function actDevelop(string $resourceName): void
    {
        Stack::insertOnTop(ST_DEVELOP_CHOOSE_FROM_HAND, ['resource' => $resourceName]);
        self::giveExtraTime(Players::getActiveId());
        Stack::finishState();
    }

    public function actTakeConnection(int $id): void
    {
        $player = Players::getActive();
        Connections::move($id, [LOCATION_HAND, $player->getId()]);
        self::giveExtraTime($player->getId());
        Notifications::connectionTaken($player, $id, Connections::getDeckName($id));
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
        // Move all actions above to postActions block
        Stack::insertOnTop(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => [RESOURCE_WORKER, RESOURCE_WORKER],
            'bonus' => [],
        ]);
        Stack::finishState();
    }

    public function actPlayConnection(int $id): void
    {
        $connection = Connections::get($id);
        $connection->activate();
        $player = Players::getActive();
        self::giveExtraTime($player->getId());
        // TODO: Move this logic to postActions() in ChooseResourceTrait to correctly notify what resource was spent
        Notifications::connectionPlayed($player, $id, $connection);
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
        Stack::finishState();
    }

    public function actOptionOpenProduction(): void
    {
        $player = Players::getActive();
        $this->getLocationFromCtx()->activate($player);
        self::giveExtraTime($player->getId());
        Stack::finishState();
    }

    public function actUseOpenProduction(string $resourceName, int $pId): void
    {
        $attacker = Players::getActive();
        $victim = Players::get($pId);
        $allCards = $victim->getBoard();
        $resource = ResourcesHelper::getResourceType($resourceName);
        $locationToActivate = $allCards->filter(function (Location $location) use ($victim, $attacker, $resource) {
            return $this->isLocationCouldBeUsedAsOpenProd($location, $attacker)
                && in_array($resource, $location->getProduct($victim));
        })->first();

        $locationToActivate->activate($attacker);
        self::giveExtraTime($attacker->getId());
        Stack::finishState();
    }

    public function actOptionRaze(): void
    {
        $this->razeOtherPlayersLocation($this->getLocationFromCtx());
        self::giveExtraTime(Players::getActiveId());
        Stack::finishState();
    }
}
