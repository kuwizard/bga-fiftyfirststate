<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class RadioactiveFuel extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RADIOACTIVE_FUEL;
        $this->name = clienttranslate("Radioactive Fuel");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_VP];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 2;
        $this->action = new Action(
            [RESOURCE_WORKER, RESOURCE_FUEL],
            [RESOURCE_VP, RESOURCE_VP],
        );
    }
}
