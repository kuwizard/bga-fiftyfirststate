<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class MercOutpost extends ActionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MERC_OUTPOST;
        $this->name = clienttranslate("Merc Outpost");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_AMMO, ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Action(
            [RESOURCE_BRICK], // TODO: Choose from any resource
            [RESOURCE_WORKER, RESOURCE_WORKER],
        );
    }
}
