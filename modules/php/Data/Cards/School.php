<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class School extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SCHOOL;
        $this->name = clienttranslate("School");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER, ICON_AMMO];
        $this->deals = [RESOURCE_WORKER];
        $this->product = [RESOURCE_WORKER];
        $this->buildingBonus = [RESOURCE_WORKER];
        $this->copies = 3;
    }
}
