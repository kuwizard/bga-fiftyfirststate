<?php

namespace STATE\Models;

use STATE\Core\Notifications;
use STATE\Managers\Locations;

class Action extends Location
{
    /**
     * @var Act
     */
    protected $action;

    /**
     * @return string
     */
    public function getFactionRow()
    {
        return 'actions';
    }

    /**
     * @return int[]
     */
    public function getSpendRequirements()
    {
        return $this->action->getSpendRequirements();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function activate($player)
    {
        $this->action->activate();
        $this->activatedTimes = $this->activatedTimes + 1;
        Locations::increaseActivatedTimes($this->id, $this->activatedTimes);
        $actionRequirements = $this->action->getSpendRequirementsUI();
        Notifications::resourcesPlacedOnLocation($player, $this->id, $actionRequirements);
    }
}
