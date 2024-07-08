<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class AbandonedSuburbs extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ABANDONED_SUBURBS;
        $this->name = clienttranslate("Abandoned Suburbs");
        $this->distance = 3;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_VP];
        $this->icons = [ICON_BRICK];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_BRICK, RESOURCE_BRICK],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
