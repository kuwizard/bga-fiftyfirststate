<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Hideout extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HIDEOUT;
        $this->name = clienttranslate("Hideout");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_VP];
        $this->copies = 2;
        $this->action = new Action(
            [], // TODO: Discard a deal to gain VP
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
