<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class ScrapMetal extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SCRAP_METAL;
        $this->name = clienttranslate("Scrap Metal");
        $this->distance = 1;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON];
        $this->icons = [ICON_IRON];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;

        $this->featureType = FEATURE_PLACE_RESOURCES;
        $this->resourceType = RESOURCE_IRON;
        $this->resourceLimit = 3;
    }
}
