<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class WreckedTank extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_WRECKED_TANK;
        $this->name = clienttranslate("Wrecked Tank");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->buildingBonus = [RESOURCE_VP]; // TODO: For each church icon
        $this->copies = 3;
    }
}
