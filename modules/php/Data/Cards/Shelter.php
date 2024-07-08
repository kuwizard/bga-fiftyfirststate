<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Shelter extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SHELTER;
        $this->name = clienttranslate("Shelter");
        $this->distance = 3;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_IRON, RESOURCE_VP];
        $this->icons = [ICON_IRON];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_IRON, RESOURCE_IRON],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
