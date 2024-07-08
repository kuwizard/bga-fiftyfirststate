<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class WeaponTrader extends ActionCard
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
        $this->action = new Action(
            [RESOURCE_GUN],
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
