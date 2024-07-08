<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class Pub extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PUB;
        $this->name = clienttranslate("Pub");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD, RESOURCE_CARD];
        $this->icons = [ICON_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->product = [RESOURCE_CARD];
        $this->isOpen = true;
        $this->buildingBonus = [RESOURCE_CARD];
        $this->copies = 2;
    }
}
