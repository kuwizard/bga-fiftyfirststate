<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Factory extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_FACTORY;
        $this->name = clienttranslate("Factory");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_IRON];
        $this->icons = [ICON_IRON, ICON_VP];
        $this->buildingBonus = [RESOURCE_VP]; // TODO: For each iron icon
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
    }
}
