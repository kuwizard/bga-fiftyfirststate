<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class Museum extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MUSEUM;
        $this->name = clienttranslate("Museum");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->product = [RESOURCE_VP]; // TODO: For each church icon
        $this->copies = 1;
    }
}
