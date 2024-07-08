<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Shadow extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SHADOW;
        $this->name = clienttranslate("Shadow");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_ARROW_RED],
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
