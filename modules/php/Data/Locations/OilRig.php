<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class OilRig extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OIL_RIG;
        $this->name = clienttranslate("Oil Rig");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 2;
    }

    public function getProduct($player)
    {
        $icons = $player->getBoardIcons(ICON_FUEL);
        $maxIcons = array_slice($icons, 0, 3);
        return array_fill(0, count($maxIcons), RESOURCE_FUEL);
    }
}
