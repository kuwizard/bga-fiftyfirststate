<?php

namespace STATE\Data\Factions;

use STATE\Models\Act;
use STATE\Models\Faction;

class Merchants extends Faction
{
    public function __construct()
    {
        parent::__construct();
        $this->type = FACTION_MERCHANTS;
        $this->name = clienttranslate("The Merchants Guild");
        $this->resources[] = RESOURCE_FUEL;
        $this->actions = [
            new Act([RESOURCE_IRON, RESOURCE_IRON], [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY]),
            new Act([RESOURCE_GUN], [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED]),
            new Act([RESOURCE_FUEL], [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE]),
        ];
    }
}
