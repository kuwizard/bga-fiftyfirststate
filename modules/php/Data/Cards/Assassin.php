<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class Assassin extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ASSASSIN;
        $this->name = clienttranslate("Assassin");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->product = [RESOURCE_GUN];
        $this->isOpen = true;
        $this->copies = 2;
    }
}
