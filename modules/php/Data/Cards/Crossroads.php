<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class Crossroads extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CROSSROADS;
        $this->name = clienttranslate("Crossroads");
        $this->distance = 1;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_CARD];
        $this->buildingBonus = [RESOURCE_CARD, RESOURCE_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 10;
    }
}
