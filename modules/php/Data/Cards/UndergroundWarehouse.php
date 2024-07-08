<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class UndergroundWarehouse extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_UNDERGROUND_WAREHOUSE;
        $this->name = clienttranslate("Underground Warehouse");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
    }

    // TODO: You may store resources here
}
