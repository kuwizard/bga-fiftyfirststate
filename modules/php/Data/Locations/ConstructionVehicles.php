<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class ConstructionVehicles extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CONSTRUCTION_VEHICLES;
        $this->name = clienttranslate("Construction Vehicles");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY];
        $this->icons = [ICON_IRON, ICON_ARROW];
        $this->deals = [RESOURCE_IRON];
        $this->product = [RESOURCE_ARROW_GREY];
        $this->isOpen = true;
        $this->buildingBonus = [RESOURCE_ARROW_GREY];
        $this->copies = 3;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {arrowGreyIcon}',
            TEXT_BONUS_DESCRIPTION => '1 {arrowGreyIcon}',
        ];
    }
}
