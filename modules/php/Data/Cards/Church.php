<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class Church extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CHURCH;
        $this->name = clienttranslate("Church");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->buildingBonus = [RESOURCE_VP, RESOURCE_VP];
        $this->copies = 2;
    }
}
