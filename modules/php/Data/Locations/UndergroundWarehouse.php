<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class UndergroundWarehouse extends Feature
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
