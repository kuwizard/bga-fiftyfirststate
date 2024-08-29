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
     * @var int
     */
    protected $activationsMax;
    /**
     * @var int
     */
    protected $activatedTimes;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->activationsMax = 1;
        $this->activatedTimes = isset($params['activated_times']) ? (int) $params['activated_times'] : null;
    }

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

    public function isActivatable()
    {
        return $this->activatedTimes < $this->activationsMax;
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

    public function jsonSerialize()
    {
        if ($this->activatedTimes === 0) {
            return parent::jsonSerialize();
        } else {
            $requirements = $this->action->getSpendRequirementsUI();
            $requirementsSingle = $requirements;
            for ($i = 0; $i < $this->activatedTimes - 1; $i++) {
                $requirements = array_merge($requirements, $requirementsSingle);
            }
            return array_merge(parent::jsonSerialize(), [
                'resources' => $requirements,
            ]);
        }
    }
}
