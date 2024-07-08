<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class ThievesCaravan extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_THIEVES_CARAVAN;
        $this->name = clienttranslate("Thieves Caravan");
        $this->distance = 3;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_CARD];
        $this->icons = [ICON_AMMO, ICON_GUN];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_WORKER, RESOURCE_WORKER],
            [], // TODO: Choose between any resource
        );
        $this->activateTimes = 2;
    }
}
