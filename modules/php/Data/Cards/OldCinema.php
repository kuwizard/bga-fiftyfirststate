<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class OldCinema extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OLD_CINEMA;
        $this->name = clienttranslate("Old Cinema");
        $this->distance = 1;
        $this->spoils = [RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->buildingBonus = [RESOURCE_VP];
        $this->copies = 1;
    }
}
