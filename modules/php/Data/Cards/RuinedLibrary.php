<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class RuinedLibrary extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RUINED_LIBRARY;
        $this->name = clienttranslate("Ruined Library");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
    }

    // TODO: You may store resources here
}
