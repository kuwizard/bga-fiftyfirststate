<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class DesertedColony extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_DESERTED_COLONY;
        $this->name = clienttranslate("Deserted Colony");
        $this->distance = 2;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_VP];
        $this->icons = [ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_WORKER],
            [RESOURCE_AMMO],
        );
        $this->activateTimes = 2;
    }
}
