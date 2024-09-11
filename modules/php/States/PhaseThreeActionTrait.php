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
            $isOpenProduction = $location instanceof Production && $location->isOpen() && $location->isActivatable();
            $razeReachable = $location->getDefenceValue() <= $player->getResource(RESOURCE_ARROW_RED);
            return !$location->isRuined() && ($isOpenProduction || $razeReachable);
        });
        $canUseBrick = !$this->getLocationsAvailableToDeploy(RESOURCE_BRICK)->empty()
            && $player->getResource(RESOURCE_BRICK, false) >= 1;
        $canUseDevel = !$this->getLocationsAvailableToDeploy(RESOURCE_DEVELOPMENT)->empty()
            && $player->getResource(RESOURCE_DEVELOPMENT, false) >= 1;
        return [
            'spendWorkers' => $player->getResource(RESOURCE_WORKER, false) >= 2,
            'factionActions' => !empty($player->getAvailableFactionActions()),
            'locations' => $player->getPlayableLocationsIds(),
            'otherPlayersLocations' => $otherPlayersLocations->getIds(),
            'deploy' => [
                'brick' => $canUseBrick,
                'development' => $canUseDevel,
            ],
            'connections' => $player->getPlayableConnectionsIds(),
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
        return ['locationId' => Stack::getCtx()['locationId']];
    }

    public function actActionPass()
    {
        self::checkAction('actActionPass');
        $player = Players::getActive();
        $player->markAsPassed();
        Notifications::playerPassed($player);
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
        Stack::finishState();
    }

    /**
     * @param int $id
     * @return void
     */
    public function actUseLocation($id)
    {
        self::checkAction('actUseLocation');
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
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_GREY, 'build');
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
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_RED, 'raze', 'getSpoils');
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
        $this->razeBuildDealCommon($location, RESOURCE_ARROW_BLUE, 'deal', 'getDeals');
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
        Notifications::handChanged($player);
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
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
        $location->activate(Players::getActive());
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
        $locationIsOpenProduction = $location instanceof Production && $location->isOpen();
        $couldBeRazed = $player->getResource(RESOURCE_ARROW_RED) >= $location->getDefenceValue();
        if ($locationIsOpenProduction && $couldBeRazed) {
            Stack::insertOnTop(ST_OPEN_PRODUCTION_OR_RAZE, ['locationId' => $id]);
        } elseif ($locationIsOpenProduction) {
            $location->activate($player);
        } elseif ($couldBeRazed) {
            $this->razeOtherPlayersLocation($player, $location);
        } else {
            throw new BgaVisibleSystemException(
                'Something went wrong during activating other player location. Is open production: ' . $locationIsOpenProduction . ', could be razed: ' . $couldBeRazed
            );
        }
        Stack::finishState();
    }

    /**
     * @param Player $attacker
     * @param Location $location
     * @return void
     */
    private function razeOtherPlayersLocation($attacker, $location)
    {
        $owner = Players::getOwner($location->getId());
        $ownerResourcesChanged = ResourcesHelper::increaseResourcesAfterAction($owner, $location->getDeals());
        Notifications::resourcesChanged($owner, $owner->getResourcesWithNames($ownerResourcesChanged));
        $attackerResourcesChanged = ResourcesHelper::increaseResourcesAfterAction($attacker, $location->getSpoils());
        $attacker->decreaseResource(RESOURCE_ARROW_RED, $location->getDefenceValue());
        $attackerResourcesChanged[] = RESOURCE_ARROW_RED;
        Notifications::resourcesChanged($attacker, $attacker->getResourcesWithNames($attackerResourcesChanged));
        $location->ruin();
        Notifications::locationRuined($owner, $location->getId());
    }

    public function actDeploy($resource)
    {
        self::checkAction('actDeploy');
        Stack::insertOnTop(ST_DEPLOY_CHOOSE_FROM_HAND, ['resource' => $resource]);
        Stack::finishState();
    }

    public function actActivateConnection($id)
    {
        self::checkAction('actActivateConnection');
        Connections::get($id)->activate();
        Notifications::connectionActivated(Players::getActive(), $id);
        Stack::finishState();
    }

    public function actOptionOpenProduction()
    {
        self::checkAction('actOptionOpenProduction');
        Locations::get(Stack::getCtx()['locationId'])->activate(Players::getActive());
        Stack::finishState();
    }

    public function actOptionRaze()
    {
        self::checkAction('actOptionRaze');
        $this->razeOtherPlayersLocation(Players::getActive(), Locations::get(Stack::getCtx()['locationId']));
        Stack::finishState();
    }
}
