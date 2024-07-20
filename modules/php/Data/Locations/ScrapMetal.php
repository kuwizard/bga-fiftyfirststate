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
        $this->buildingBonus = []; // TODO: place 3 Iron here
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
    }
}
