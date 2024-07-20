<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class RubbleTrader extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RUBBLE_TRADER;
        $this->name = clienttranslate("Rubble Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_CARD];
        $this->icons = [ICON_BRICK, ICON_VP];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_BRICK],
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
