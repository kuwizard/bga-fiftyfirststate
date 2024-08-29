<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class WeaponTrader extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_WEAPON_TRADER;
        $this->name = clienttranslate("Weapon Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_GUN],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
    }
}
