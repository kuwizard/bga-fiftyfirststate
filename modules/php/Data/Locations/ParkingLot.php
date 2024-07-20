<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class ParkingLot extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PARKING_LOT;
        $this->name = clienttranslate("Parking Lot");
        $this->distance = 2;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_IRON];
        $this->icons = [ICON_IRON, ICON_AMMO];
        $this->deals = [RESOURCE_IRON];
        $this->product = [RESOURCE_IRON]; // TODO: ...for each iron icon
        $this->copies = 2;
    }
}
