<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class Excavator extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_EXCAVATOR;
        $this->name = clienttranslate("Excavator");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_VP];
        $this->icons = [ICON_BRICK, ICON_ARROW];
        $this->deals = [RESOURCE_BRICK];
        $this->product = [RESOURCE_DEVELOPMENT];
        $this->isOpen = true;
        $this->copies = 1;
    }
}
