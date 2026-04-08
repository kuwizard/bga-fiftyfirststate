<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Production;

class FuelTank extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_FUEL_TANK;
        $this->name = clienttranslate("Fuel Tank");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->product = [RESOURCE_FUEL];
        $this->isOpen = true;
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {fuelIcon}',
        ];
    }
}
