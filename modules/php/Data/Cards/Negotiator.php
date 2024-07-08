<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Negotiator extends ActionCard
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
        $this->action = new Action(
            [RESOURCE_WORKER], // TODO: ...and discard a Deal
            [RESOURCE_VP, RESOURCE_VP],
        );
    }
}
