<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Refinery extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_REFINERY;
        $this->name = clienttranslate("Refinery");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
    }

    public function getBuildingBonus($player)
    {
        return $this->getVPForEachIcon($player, ICON_FUEL);
    }
}
