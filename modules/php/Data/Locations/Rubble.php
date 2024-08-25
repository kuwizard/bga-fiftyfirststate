<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Rubble extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RUBBLE;
        $this->name = clienttranslate("Rubble");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK, ICON_VP];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
    }

    public function getBuildingBonus($player)
    {
        return $this->getVPForEachIcon($player, ICON_BRICK);
    }
}
