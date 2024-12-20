<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Feature;

class Truck extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_TRUCK;
        $this->name = clienttranslate("Truck");
        $this->distance = 1;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO];
        $this->icons = [ICON_ARROW, ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => 'Each time you Raze or make a Deal gain 1 {ammoIcon}',
        ];
    }
}
