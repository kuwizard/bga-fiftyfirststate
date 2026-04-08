<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Production;

class OilRig extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OIL_RIG;
        $this->name = clienttranslate("Oil Rig");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {fuelIcon} for each {fuelAcon} in your State. Max. 3'),
        ];
    }

    public function getProduct($player): array
    {
        $icons = $player->getBoardIcons(ICON_FUEL);
        $maxIcons = array_slice($icons, 0, 3);
        return array_fill(0, count($maxIcons), RESOURCE_FUEL);
    }
}
