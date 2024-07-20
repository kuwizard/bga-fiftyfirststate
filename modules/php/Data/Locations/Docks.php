<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class Docks extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_DOCKS;
        $this->name = clienttranslate("Docks");
        $this->distance = 1;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON];
        $this->icons = [ICON_IRON, ICON_AMMO];
        $this->deals = [RESOURCE_IRON];
        $this->product = [RESOURCE_IRON];
        $this->isOpen = true;
        $this->copies = 2;
    }
}
