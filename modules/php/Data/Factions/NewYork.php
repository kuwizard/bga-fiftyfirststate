<?php

namespace STATE\Data\Factions;

use STATE\Models\Action;
use STATE\Models\Faction;

class NewYork extends Faction
{
    public function __construct()
    {
        parent::__construct();
        $this->type = FACTION_NEW_YORK;
        $this->name = clienttranslate("New York");
        $this->resources[] = RESOURCE_IRON;
        $this->actions = [
            new Action([RESOURCE_IRON], [RESOURCE_ARROW_GREY]),
            new Action([RESOURCE_GUN], [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED]),
            new Action([RESOURCE_FUEL, RESOURCE_FUEL], [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE]),
        ];
    }
}
