<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class FuelTank extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_FUEL_TANK;
        $this->name = clienttranslate("Fuel Tank");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->isOpen = true;
        $this->copies = 2;
    }
}
