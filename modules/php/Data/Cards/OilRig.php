<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class OilRig extends ProductionCard
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
        $this->product = [RESOURCE_FUEL]; // TODO: For each fuel icon
        $this->copies = 2;
    }
}
