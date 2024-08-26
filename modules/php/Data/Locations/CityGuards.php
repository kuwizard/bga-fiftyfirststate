<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class CityGuards extends Feature
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

        $this->featureType = FEATURE_PLACE_RESOURCES;
        $this->resourceType = RESOURCE_GUN;
        $this->resourceStartAmount = 3;
    }
}
