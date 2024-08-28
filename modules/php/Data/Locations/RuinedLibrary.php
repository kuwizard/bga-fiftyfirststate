<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class RuinedLibrary extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RUINED_LIBRARY;
        $this->name = clienttranslate("Ruined Library");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;

        $this->featureType = FEATURE_STORE_RESOURCES;
        $this->resourceLimit = 3;
        $this->resourcesOptions = [RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK];
    }
}
