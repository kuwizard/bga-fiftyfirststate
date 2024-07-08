<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class Refinery extends FeatureCard
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
        $this->buildingBonus = [RESOURCE_VP]; // TODO: For each fuel icon
        $this->copies = 1;
    }
}
