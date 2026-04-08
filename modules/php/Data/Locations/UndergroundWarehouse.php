<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageMultiple;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionMulti;

class UndergroundWarehouse extends FeatureStorageMultiple
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_UNDERGROUND_WAREHOUSE;
        $this->name = clienttranslate("Underground Warehouse");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_AMMO];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;

        $this->resourcesOptions = [
            new ResourceStorageOptionMulti([RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK], 3),
        ];
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate(
                'You may store up to 3 {gunIcon} / {fuelIcon} / {ironIcon} / {brickIcon} here during the Cleanup phase. Take them back during the next Production phase'
            ),
        ];
    }
}
