<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class Museum extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MUSEUM;
        $this->name = clienttranslate("Museum");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->product = [RESOURCE_VP]; // TODO: For each church icon
        $this->copies = 1;
    }
}
