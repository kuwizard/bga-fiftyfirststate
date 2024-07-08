<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class ConstructionVehicles extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CONSTRUCTION_VEHICLES;
        $this->name = clienttranslate("Construction Vehicles");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY];
        $this->icons = [ICON_IRON, ICON_ARROW];
        $this->deals = [RESOURCE_IRON];
        $this->product = [RESOURCE_ARROW_GREY];
        $this->isOpen = true;
        $this->buildingBonus = [RESOURCE_ARROW_GREY];
        $this->copies = 3;
    }
}
