<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class GasolineCultist extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GASOLINE_CULTIST;
        $this->name = clienttranslate("Gasoline Cultist");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_BLUE, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_ARROW_BLUE],
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
