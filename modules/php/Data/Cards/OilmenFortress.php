<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class OilmenFortress extends ActionCard
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
        $this->copies = 20;
        $this->action = new Action(
            [RESOURCE_WORKER, RESOURCE_AMMO, RESOURCE_AMMO],
            [RESOURCE_VP],
        );
    }
}
