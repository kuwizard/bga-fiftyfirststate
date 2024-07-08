<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class HugeMachinery extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HUGE_MACHINERY;
        $this->name = clienttranslate("Huge Machinery");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY];
        $this->icons = [ICON_IRON, ICON_ARROW];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 2;
        $this->action = new Action(
            [RESOURCE_WORKER, RESOURCE_IRON],
            [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY],
        );
    }
}
