<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Motel extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MOTEL;
        $this->name = clienttranslate("Motel");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD, RESOURCE_VP];
        $this->icons = [ICON_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_WORKER],
            [RESOURCE_CARD],
        );
        $this->activateTimes = 2;
    }
}
