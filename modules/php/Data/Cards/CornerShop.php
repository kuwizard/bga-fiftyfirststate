<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class CornerShop extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CORNER_SHOP;
        $this->name = clienttranslate("Corner shop");
        $this->distance = 1;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO];
        $this->icons = [ICON_AMMO, ICON_VP];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 2;
        $this->action = new Action(
            [RESOURCE_WORKER], // TODO: Add choice between gun/fuel/iron/brick
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
