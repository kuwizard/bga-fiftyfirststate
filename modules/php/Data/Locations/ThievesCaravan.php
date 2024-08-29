<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class ThievesCaravan extends Action
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
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_WORKER],
            [], // TODO: Choose between any resource
        );
        $this->activationsMax = 2;
    }
}
