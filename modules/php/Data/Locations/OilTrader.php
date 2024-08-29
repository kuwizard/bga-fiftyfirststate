<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class OilTrader extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OIL_TRADER;
        $this->name = clienttranslate("Oil Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_FUEL],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
    }
}
