<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class Negotiator extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_NEGOTIATOR;
        $this->name = clienttranslate("Negotiator");
        $this->distance = 3;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER], // TODO: ...and discard a Deal
            [RESOURCE_VP, RESOURCE_VP],
        );
    }
}
