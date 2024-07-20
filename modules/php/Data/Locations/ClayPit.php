<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class ClayPit extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CLAY_PIT;
        $this->name = clienttranslate("Clay Pit");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_VP];
        $this->icons = [ICON_BRICK, ICON_VP];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_BRICK],
            [RESOURCE_VP, RESOURCE_VP],
        );
    }
}
