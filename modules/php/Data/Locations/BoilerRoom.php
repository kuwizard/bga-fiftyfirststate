<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class BoilerRoom extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BOILER_ROOM;
        $this->name = clienttranslate("Boiler Room");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->product = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->buildingBonus = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->isOpen = true;
    }
}
