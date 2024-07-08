<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Confessor extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CONFESSOR;
        $this->name = clienttranslate("Confessor");
        $this->distance = 3;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_GUN, RESOURCE_VP];
        $this->icons = [ICON_GUN];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_GUN, RESOURCE_GUN],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
