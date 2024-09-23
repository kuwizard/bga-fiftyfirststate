<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class OilmenFortress extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OILMEN_FORTRESS;
        $this->name = clienttranslate("Oilmen Fortress");
        $this->distance = 3;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_VP];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_FUEL, RESOURCE_FUEL],
            [RESOURCE_VP, RESOURCE_VP, RESOURCE_VP],
        );
    }
}
