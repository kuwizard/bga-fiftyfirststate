<?php

namespace Bga\Games\Fiftyfirststate\Data\Factions;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Faction;

class NewYork extends Faction
{
    public function __construct()
    {
        parent::__construct();
        $this->type = FACTION_NEW_YORK;
        $this->name = clienttranslate("New York");
        $this->resources[] = RESOURCE_IRON;
        $this->actions = [
            new Act([RESOURCE_IRON], [RESOURCE_ARROW_GREY]),
            new Act([RESOURCE_GUN], [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED]),
            new Act([RESOURCE_FUEL, RESOURCE_FUEL], [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE]),
        ];
    }
}
