<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class Archive extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ARCHIVE;
        $this->name = clienttranslate("Archive");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER, ICON_VP];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_WORKER],
            [RESOURCE_VP],
        );
        $this->activateTimes = 2;
    }
}
