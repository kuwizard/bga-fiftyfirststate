<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Museum extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MUSEUM;
        $this->name = clienttranslate("Museum");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->copies = 1;
    }

    public function getBuildingBonus($player)
    {
        return $this->getVPForEachIcon($player, ICON_CHURCH);
    }
}
