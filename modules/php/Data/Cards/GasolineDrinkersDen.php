<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class GasolineDrinkersDen extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GASOLINE_DEN;
        $this->name = clienttranslate("Gasoline Drinkers Den");
        $this->distance = 3;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_VP];
        $this->icons = [ICON_FUEL];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_FUEL, RESOURCE_FUEL],
            [RESOURCE_VP, RESOURCE_VP],
        );
    }
}
