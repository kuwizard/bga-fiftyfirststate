<?php

namespace STATE\Models;

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
     * @return bool
     */
    public function isActivatable()
    {
        return !$this->isRuined() && $this->activatedTimes < $this->activationsMax;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function activate($player)
    {
        $this->action->activate($this->id);
        $this->activatedTimes = $this->activatedTimes + 1;
        Locations::increaseActivatedTimes($this->id, $this->activatedTimes);
    }

    public function getDefenceValue()
    {
        return 5;
    }
}
