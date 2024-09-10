<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageMultiple;
use STATE\Models\ResourceStorageOption;

class UndergroundWarehouse extends FeatureStorageMultiple
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

        $this->resourcesOptions = [
            new ResourceStorageOption([RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK], 3),
        ];
    }
}
