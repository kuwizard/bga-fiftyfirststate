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
            $isActivatableOpenProduction = $location instanceof Production
                && $location->isOpen()
                && $location->isActivatable()
                && $player->getResource(RESOURCE_WORKER, false) > 0;
            $razeReachable = $location->getDefenceValue() <= $player->getResource(RESOURCE_ARROW_RED);
            return !$location->isRuined() && ($isActivatableOpenProduction || $razeReachable);
        });
        $connectionsToTake = $player->getResource(RESOURCE_WORKER) >= 2 ? Connections::getBothAvailable()->getIds() : [];
        return [
            'spendWorkers' => $player->getResource(RESOURCE_WORKER, false) >= 2,
            'factionActions' => !empty($player->getAvailableFactionActions()),
            'locations' => $player->getPlayableLocationsWithCardWarnings(),
            'otherPlayersLocations' => $this->mapLocationsWithCardGetting($otherPlayersLocations->toArray(), $player),
            'deploy' => $this->whatCanBeUsedForDevel($player),
            'connectionsToTake' => $connectionsToTake,
            'connectionsToPlay' => $player->getPlayableConnectionsIds(),
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
        return $location->getDistance() <= $player->getResource(RESOURCE_ARROW_RED)
            && in_array(RESOURCE_CARD, $location->getSpoils());
    }

    private function whatCanBeUsedForDevel(Player $player): array
    {
        $locationsToDeployWithBrick = $this->getLocationsAvailableToDeploy(RESOURCE_BRICK);
        return [
            'brick' => !$locationsToDeployWithBrick->empty() && $player->getResource(RESOURCE_BRICK, false) >= 1,
            'development' => $player->getResource(RESOURCE_DEVELOPMENT, false) >= 1
                && !$this->getLocationsAvailableToDeploy(RESOURCE_DEVELOPMENT)->empty(),
            'ammo' => !$locationsToDeployWithBrick->empty()
                && $player->getResource(RESOURCE_AMMO, false) >= 1
                && $player->getResource(RESOURCE_BRICK, false) === 0,
        ];
    }

    public function argFactionActions()
    {
        return Players::getActive()->getAvailableFactionActions();
    }

    public function argLocationActions()
    {
        $locationId = Stack::getCtx()['locationId'];
        $location = Locations::get($locationId);
        return [
            'id' => $locationId,
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
        $location = Locations::get(Stack::getCtx()['locationId']);
        $player = Players::getActive();
        return [
            'locationId' => $location->getId(),
            'raze' => $this->razeReachableCardInSpoils($location, $player),
            'openProd' => $this->razeReachableCardInSpoils($location, $player),
        ];
    }

    public function actActionPass()
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
        }
        Stack::finishState();
    }

    public function actSpendWorkers()
    {
        self::checkAction('actSpendWorkers');
        Stack::insertOnTopAndFinish(ST_SPEND_WORKERS);
    }

    public function actUndo()
    {
        self::checkAction('actUndo');
        Stack::insertOnTopAndFinish(ST_PHASE_THREE_ACTION);
    }

    public function actGainResourceForWorkers($resourceName)
    {
        self::checkAction('actGainResourceForWorkers');
        Stack::insertOnTopAndFinish(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => [RESOURCE_WORKER, RESOURCE_WORKER],
            'bonus' => [ResourcesHelper::getResourceType($resourceName)],
            // 3 is a magic number for spending workers. 0-2 are for faction actions
            'activatorId' => Players::getActive()->getFaction() + 3,
        ]);
    }

    public function actEnableFactionActions()
    {
        self::checkAction('actEnableFactionActions');
        Stack::insertOnTopAndFinish(ST_FACTION_ACTIONS);
    }

    /**
     * @param int $id
     * @return void
     */
    public function actFactionAct($id)
    {
        self::checkAction('actFactionAct');
        $player = Players::getActive();
        /** @var Act $actionChosen */
        $actionChosen = $player->getAvailableFactionActions()[$id];
        $actionChosen->activate($id + $player->getFaction());
        Factions::setAsUsed($player->getFaction(), $id);
        self::giveExtraTime($player->getId());
        Stack::finishState();
    }

    /**
     * @param int $id
     * @return void
     */
    public function actUseLocation($id)
    {
        self::checkAction('actUseLocation');
        self::giveExtraTime(Players::getActiveId());
        Stack::insertOnTopAndFinish(ST_LOCATION_ACTIONS, ['locationId' => $id]);
    }

    /**
     * @param int $id
     * @return void
     */
    public function actLocationBuild()
    {
        self::checkAction('actLocationBuild');
        $locationId = Stack::getCtx()['locationId'];
        $location = Locations::get($locationId);
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_GREY, LOCATION_ACTION_BUILD);
    }

    /**
     * @param int $id
     * @return void
     */
    public function actLocationRaze()
    {
        self::checkAction('actLocationRaze');
        $locationId = Stack::getCtx()['locationId'];
        $location = Locations::get($locationId);
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_RED, LOCATION_ACTION_RAZE, 'getSpoils');
    }

    /**
     * @param int $id
     * @return void
     */
    public function actLocationDeal()
    {
        self::checkAction('actLocationDeal');
        $locationId = Stack::getCtx()['locationId'];
        $location = Locations::get($locationId);
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_BLUE, LOCATION_ACTION_DEAL, 'getDeals');
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

    /**
     * @param int $id
     * @return void
     */
    public function actDiscardLocation($id)
    {
        self::checkAction('actDiscardLocation');
        $player = Players::getActive();
        $player->discard([$id]);
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
        Notifications::locationDiscarded($player, Locations::get($id));
        self::giveExtraTime($player->getId());
        Stack::finishState();
    }

    /**
     * @param int $id
     * @return void
     */
    public function actActivateLocation($id)
    {
        self::checkAction('actActivateLocation');
        $location = Locations::get($id);
        $player = Players::getActive();
        $location->activate($player);
        self::giveExtraTime($player->getId());
        Stack::finishState();
    }

    /**
     * @param int $id
     * @return void
     */
    public function actUseOtherPlayerLocation($id)
    {
        self::checkAction('actUseOtherPlayerLocation');
        $location = Locations::get($id);
        $player = Players::getActive();
        $locationIsOpenProduction = $location instanceof Production && $location->isOpen() && $location->isActivatable();
        $couldBeRazed = $player->getResource(RESOURCE_ARROW_RED, false, true) >= $location->getDefenceValue();
        if ($locationIsOpenProduction && $couldBeRazed) {
            Stack::insertOnTop(ST_OPEN_PRODUCTION_OR_RAZE, ['locationId' => $id]);
        } elseif ($locationIsOpenProduction) {
            $location->activate($player);
        } elseif ($couldBeRazed) {
            $this->razeOtherPlayersLocation($location);
        } else {
            throw new \BgaVisibleSystemException(
                'Something went wrong during activating other player location. Is open production: ' . $locationIsOpenProduction . ', could be razed: ' . $couldBeRazed
            );
        }
        Stack::finishState();
    }

    private function razeOtherPlayersLocation(Location $location)
    {
        Stack::insertOnTop(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => array_fill(0, $location->getDefenceValue(), RESOURCE_ARROW_RED),
            'bonus' => $location->getSpoils(),
            'postActions' => ['type' => LOCATION_ACTION_RAZE_OTHER, 'id' => $location->getId()],
        ]);

    }

    public function actDeploy($resource)
    {
        self::checkAction('actDeploy');
        Stack::insertOnTop(ST_DEPLOY_CHOOSE_FROM_HAND, ['resource' => $resource]);
        self::giveExtraTime(Players::getActiveId());
        Stack::finishState();
    }

    public function actTakeConnection(int $id)
    {
        $player = Players::getActive();
        Connections::move($id, [LOCATION_HAND, $player->getId()]);
        self::giveExtraTime($player->getId());
        Notifications::connectionTaken($player, $id, Connections::getDeckName($id));
        // Move all actions above to postActions block
        Stack::insertOnTop(ST_CREATE_RESOURCE_SOURCE_MAP, [
            'spend' => [RESOURCE_WORKER, RESOURCE_WORKER],
            'bonus' => [],
        ]);
        Stack::finishState();
    }

    public function actPlayConnection(int $id)
    {
        $connection = Connections::get($id);
        $connection->activate();
        $player = Players::getActive();
        self::giveExtraTime($player->getId());
        Notifications::connectionPlayed($player, $id, $connection);
        Stack::finishState();
    }

    public function actOptionOpenProduction()
    {
        self::checkAction('actOptionOpenProduction');
        Locations::get(Stack::getCtx()['locationId'])->activate(Players::getActive());
        self::giveExtraTime(Players::getActiveId());
        Stack::finishState();
    }

    public function actOptionRaze()
    {
        self::checkAction('actOptionRaze');
        $this->razeOtherPlayersLocation(Locations::get(Stack::getCtx()['locationId']));
        self::giveExtraTime(Players::getActiveId());
        Stack::finishState();
    }
}
