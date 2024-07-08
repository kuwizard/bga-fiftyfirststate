<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class ScrapTrader extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SCRAP_TRADER;
        $this->name = clienttranslate("Scrap Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_CARD];
        $this->icons = [ICON_IRON, ICON_VP];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_IRON],
            [RESOURCE_VP],
        );
    }
}
