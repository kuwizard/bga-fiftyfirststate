<?php

namespace STATE\Models;

use STATE\Core\Notifications;
use STATE\Managers\Locations;
use STATE\Managers\Players;

class Production extends Location
{
    /**
     * @var boolean
     */
    protected $isOpen;
    /**
     * @var int[]
     */
    protected $product;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->isOpen = false;
    }

    /**
     * @param Player $player
     * @return int[]
     */
    public function getProduct($player)
    {
        return $this->isRuined() ? [] : $this->product;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public function getSpendRequirements()
    {
        return [RESOURCE_WORKER];
    }

    public function isActivatable()
    {
        return !$this->isRuined() && $this->activatedTimes < 1;
    }

    public function getDefenceValue()
    {
        return 3;
    }

    public function activate($player)
    {
        (new Act($this->getSpendRequirements(), $this->getProduct($player)))->activate($this->id);
        $this->activatedTimes = $this->activatedTimes + 1;
        Locations::increaseActivatedTimes($this->id, $this->activatedTimes);
        $productionOwner = Players::getOwner($this->id);
        $productionOwner->increaseResource(RESOURCE_WORKER);
        Notifications::resourcesChanged($productionOwner, $productionOwner->getResourcesWithNames([RESOURCE_WORKER]));
    }

    public function getFactionRow()
    {
        return 'production';
    }

    public function getFactionRowName(): string
    {
        return clienttranslate('Production');
    }

    public function getRowText(): string
    {
        return $this->isOpen() ? clienttranslate('OPEN PRODUCTION') : clienttranslate('PRODUCTION');
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'isOpen' => $this->isOpen,
        ]);
    }
}
