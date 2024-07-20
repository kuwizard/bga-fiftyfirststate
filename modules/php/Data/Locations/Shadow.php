<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class Shadow extends Action
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
        $this->action = new Act(
            [RESOURCE_ARROW_RED],
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
