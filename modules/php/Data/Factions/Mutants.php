<?php

namespace STATE\Data\Factions;

use STATE\Models\Action;
use STATE\Models\Faction;

class Mutants extends Faction
{
    public function __construct()
    {
        parent::__construct();
        $this->type = FACTION_MUTANTS;
        $this->name = clienttranslate("Mutants Union");
        $this->resources[] = RESOURCE_GUN;
        $this->actions = [
            new Action([RESOURCE_IRON, RESOURCE_IRON], [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY]),
            new Action([RESOURCE_GUN], [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED, RESOURCE_ARROW_RED]),
            new Action([RESOURCE_FUEL], [RESOURCE_ARROW_BLUE]),
        ];
    }
}
