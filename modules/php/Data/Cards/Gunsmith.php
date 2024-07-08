<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Gunsmith extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GUNSMITH;
        $this->name = clienttranslate("Gunsmith");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED];
        $this->icons = [ICON_GUN, ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_GUN],
            [RESOURCE_ARROW_RED],
        );
        $this->activateTimes = 2;
    }
}
