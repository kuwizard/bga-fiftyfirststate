<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Convoy extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CONVOY;
        $this->name = clienttranslate("Convoy");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE];
        $this->icons = [ICON_FUEL, ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_BLUE];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_FUEL],
            [RESOURCE_ARROW_BLUE],
        );
        $this->activateTimes = 2;
    }
}
