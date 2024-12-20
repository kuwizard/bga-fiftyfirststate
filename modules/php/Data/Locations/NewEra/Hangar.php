<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Production;

class Hangar extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HANGAR;
        $this->name = clienttranslate("Hangar");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_AMMO];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->product = [RESOURCE_FUEL, RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {fuelIcon} and 1 {ammoIcon}'),
        ];
    }
}
