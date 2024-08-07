<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\Resources;
use STATE\Managers\Players;
use STATE\Models\Act;

trait PhaseThreeActionTrait
{

    public function argPhaseThreeAction()
    {
        $player = Players::getActive();
        $spendWorkers = $player->getResource(RESOURCE_WORKER) >= 2;
        return ['spendWorkers' => $spendWorkers, 'factionActions' => !empty($player->getAvailableActions())];
    }

    public function argFactionActions()
    {
        return $this->addIdsToActions(Players::getActive()->getAvailableActions());
    }

    private function addIdsToActions($actions)
    {
        $id = 0;
        foreach ($actions as $action) {
            /** @var Act $action */
            $action->setId($id);
            $id = $id + 1;
        }
        return $actions;
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
        $actionsWithIds = $this->addIdsToActions($player->getAvailableActions());
        /** @var Act $actionChosen */
        $actionChosen = array_column($actionsWithIds, null, 'id')[$id];
        $actionChosen->activate($player);
        Stack::finishState();
    }
}
