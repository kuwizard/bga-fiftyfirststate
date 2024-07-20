<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class ThievesDen extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_THIEVES_DEN;
        $this->name = clienttranslate("Thieves Den");
        $this->distance = 1;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
    }

    // TODO: You may store resources and workers here
}
