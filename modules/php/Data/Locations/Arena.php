<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class Arena extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ARENA;
        $this->name = clienttranslate("Arena");
        $this->distance = 2;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->product = [RESOURCE_GUN]; // TODO: ...for each gun icon
        $this->copies = 2;
    }
}
