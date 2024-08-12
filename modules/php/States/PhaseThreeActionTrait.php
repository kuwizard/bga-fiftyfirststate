<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\Resources;
use STATE\Managers\Factions;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Act;
use STATE\Models\Location;

trait PhaseThreeActionTrait
{

    public function argPhaseThreeAction()
    {
        $player = Players::getActive();
        $spendWorkers = $player->getResource(RESOURCE_WORKER) >= 2;
        return [
            'spendWorkers' => $spendWorkers,
            'factionActions' => !empty($player->getAvailableFactionActions()),
            'locations' => $player->getPlayableLocationsIds(),
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

    public function actActionPass()
    {
        self::checkAction('actActionPass');
        Players::getActive()->markAsPassed();
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
        $player = Players::getActive();
        $resourceType = Resources::getResourceType($resourceName);
        $player->increaseResource($resourceType);
        $player->decreaseResource(RESOURCE_WORKER, 2);
        $notificationData = [Resources::getResourceName(RESOURCE_WORKER) => $player->getResource(RESOURCE_WORKER)];
        if ($resourceType === RESOURCE_CARD) {
            $notificationData[$resourceName] = $player->getHandAmount();
            Notifications::handChanged($player);
        } else {
            $notificationData[$resourceName] = $player->getResource($resourceType);
        }
        Notifications::resourcesChanged($player, $notificationData);
        Stack::finishState();
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
        $actionChosen->activate($player);
        Factions::setAsUsed($player->getFaction(), $id);
        Notifications::resourcesSpentFaction($player, $actionChosen->getSpendRequirementsUI(), $id);
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
        $this->razeBuildDealCommon(RESOURCE_ARROW_GREY, LOCATION_BOARD);
    }

    /**
     * @param int $id
     * @return void
     */
    public function actLocationRaze()
    {
        self::checkAction('actLocationRaze');
        $this->razeBuildDealCommon(RESOURCE_ARROW_RED, LOCATION_DISCARD, 'getSpoils');
    }

    /**
     * @param int $id
     * @return void
     */
    public function actLocationDeal()
    {
        self::checkAction('actLocationDeal');
        $this->razeBuildDealCommon(RESOURCE_ARROW_BLUE, LOCATION_DEALS, 'getDeals');
    }

    /**
     * @param int $decrease
     * @param string $whereToMove
     * @param string $increase
     * @param Location|null $location
     * @return void
     */
    private function razeBuildDealCommon($decrease, $whereToMove, $increase = null)
    {
        $locationId = Stack::getCtx()['locationId'];
        $location = Locations::get($locationId);
        $player = Players::getActive();
        /** @var Location $location */
        $player->decreaseResource($decrease, $location->getDistance());
        $resourcesChanged = [$decrease];
        if ($increase) {
            foreach (array_count_values($location->{$increase}()) as $resource => $amount) {
                $resourcesChanged[] = $resource;
                $player->increaseResource($resource, $amount);
            }
        }
        Locations::move($locationId, [$whereToMove, $player->getId()]);
        Notifications::resourcesChanged($player, $player->getResourcesWithNames($resourcesChanged));
        Stack::finishState();
    }
}
