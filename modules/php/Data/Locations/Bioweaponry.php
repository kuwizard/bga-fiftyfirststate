<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class Bioweaponry extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BIOWEAPONRY;
        $this->name = clienttranslate("Bioweaponry");
        $this->distance = 2;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_VP];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_GUN],
            [RESOURCE_VP, RESOURCE_VP],
        );
    }
}
