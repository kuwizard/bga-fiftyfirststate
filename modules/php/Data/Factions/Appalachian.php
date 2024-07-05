<?php

namespace STATE\Data\Factions;

use STATE\Models\Action;
use STATE\Models\Faction;

class Appalachian extends Faction
{
    public function __construct()
    {
        parent::__construct();
        $this->type = FACTION_APPALACHIAN;
        $this->name = clienttranslate("Appalachian Federation");
        $this->resources[] = RESOURCE_BRICK;
        $this->actions = [
            new Action([RESOURCE_BRICK, RESOURCE_CARD], [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY]),
            new Action([RESOURCE_GUN], [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED]),
            new Action([RESOURCE_FUEL], [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE]),
        ];
    }
}
