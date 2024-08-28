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

        $this->featureType = FEATURE_STORE_RESOURCES;
        $this->resourceLimit = 3;
        $this->resourcesOptions = [RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK];
    }
}
