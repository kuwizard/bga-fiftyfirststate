<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class MurderersPub extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MURDERERS_PUB;
        $this->name = clienttranslate("Murderers' Pub");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_GUN];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->buildingBonus = [RESOURCE_VP]; // TODO: For each gun icon
        $this->copies = 1;
    }
}
