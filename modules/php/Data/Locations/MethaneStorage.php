<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class MethaneStorage extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_METHANE_STORAGE;
        $this->name = clienttranslate("Methane Storage");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL];
        $this->buildingBonus = []; // TODO: place 3 fuel here
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
    }
}
