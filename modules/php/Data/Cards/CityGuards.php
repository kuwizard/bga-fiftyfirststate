<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class CityGuards extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CITY_GUARDS;
        $this->name = clienttranslate("City Guards");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN];
        $this->buildingBonus = []; // TODO: place 3 guns here
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
    }
}
