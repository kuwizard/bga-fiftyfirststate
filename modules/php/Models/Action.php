<?php

namespace STATE\Models;

use STATE\Core\Stack;
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

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->activationsMax = 1;
    }

    protected function getText(bool $isBuildingBonus = false): array
    {
        return [
            ...parent::getText(),
            TEXT_MAY_BE_ACTIVATED_TWICE => $this->activationsMax === 2 ? clienttranslate('May be activated twice.') : '',
        ];
    }

    /**
     * @return string
     */
    public function getFactionRow()
    {
        return 'actions';
    }

    public function getFactionRowName(): string
    {
        return clienttranslate('Actions');
    }

    public function getRowText(): string
    {
        return clienttranslate('ACTION');
    }

    public function getSpendRequirements(): array
    {
        return $this->action->getSpendRequirements();
    }

    public function isActivatable(): bool
    {
        return !$this->isRuined() && $this->activatedTimes < $this->activationsMax;
    }

    public function getBonus(): array
    {
        return $this->action->getBonus();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function activate($player)
    {
        $newActivatedTimes = $this->activatedTimes + 1;
        if ($newActivatedTimes < $this->activationsMax) {
            Stack::insertOnTop(ST_ACTIVATE_SECOND_TIME, ['locationId' => $this->id]);
        }
        $this->action->activate($this->id);
    }

    public function postActivation()
    {
        $this->activatedTimes = $this->activatedTimes + 1;
        Locations::increaseActivatedTimes($this->id, $this->activatedTimes);
    }

    public function getDefenceValue(): int
    {
        return 5 + parent::getDefenceValue();
    }
}
