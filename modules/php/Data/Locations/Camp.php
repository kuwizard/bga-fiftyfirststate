<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Camp extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CAMP;
        $this->name = clienttranslate("Camp");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;

        $this->featureType = FEATURE_PLACE_RESOURCES;
        $this->resourceType = RESOURCE_WORKER;
        $this->resourceLimit = 3;
    }
}
