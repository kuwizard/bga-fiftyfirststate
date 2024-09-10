<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageSingle;

class CityGuards extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CITY_GUARDS;
        $this->name = clienttranslate("City Guards");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;

        $this->resourceType = RESOURCE_GUN;
        $this->resourceLimit = 3;
    }
}
