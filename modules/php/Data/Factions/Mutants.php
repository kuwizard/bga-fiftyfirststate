<?php

namespace Bga\Games\Fiftyfirststate\Data\Factions;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Faction;

class Mutants extends Faction
{
    public function __construct()
    {
        parent::__construct();
        $this->type = FACTION_MUTANTS;
        $this->name = clienttranslate("Mutants Union");
        $this->resources[] = RESOURCE_GUN;
        $this->actions = [
            new Act([RESOURCE_IRON, RESOURCE_IRON], [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY]),
            new Act([RESOURCE_GUN], [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED, RESOURCE_ARROW_RED]),
            new Act([RESOURCE_FUEL], [RESOURCE_ARROW_BLUE]),
        ];
    }
}
