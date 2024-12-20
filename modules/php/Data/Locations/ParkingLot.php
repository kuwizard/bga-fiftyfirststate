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
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {ironIcon} for each {ironAcon} in your State. Max. 3'),
        ];
    }

    public function getProduct($player): array
    {
        $icons = $player->getBoardIcons(ICON_IRON);
        $maxIcons = array_slice($icons, 0, 3);
        return array_fill(0, count($maxIcons), RESOURCE_IRON);
    }
}
