<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class Sharrash extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SHARRASH;
        $this->name = clienttranslate("Sharrash");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_UNIVERSAL, RESOURCE_ARROW_UNIVERSAL];
        $this->icons = [ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_UNIVERSAL];
        $this->product = [RESOURCE_ARROW_UNIVERSAL];
        $this->isOpen = true;
        $this->buildingBonus = [RESOURCE_ARROW_UNIVERSAL];
        $this->copies = 1;
    }
}
