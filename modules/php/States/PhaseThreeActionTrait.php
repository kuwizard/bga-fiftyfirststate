<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\Resources;
use STATE\Managers\Players;

trait PhaseThreeActionTrait
{

    public function argPhaseThreeAction()
    {
        $spendWorkers = Players::getActive()->getResource(RESOURCE_WORKER) >= 2;
        return ['spendWorkers' => $spendWorkers];
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
        Stack::insertOnTop(ST_SPEND_WORKERS);
        Stack::finishState();
    }

    public function actUndoSpend()
    {
        self::checkAction('actUndoSpend');
        Stack::insertOnTop(ST_PHASE_THREE_ACTION);
        Stack::finishState();
    }

    public function actGainResource($resourceName)
    {
        self::checkAction('actGainResource');
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
}
