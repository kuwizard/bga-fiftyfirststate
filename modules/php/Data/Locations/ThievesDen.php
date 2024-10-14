<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageMultiple;
use STATE\Models\ResourceStorageOption;

class ThievesDen extends FeatureStorageMultiple
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_THIEVES_DEN;
        $this->name = clienttranslate("Thieves Den");
        $this->distance = 1;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;

        $this->resourcesOptions = [
            new ResourceStorageOption([RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK], 2),
            new ResourceStorageOption([RESOURCE_WORKER], 2),
        ];
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate(
                'You may store up to 2 {gunIcon} / {fuelIcon} / {ironIcon} / {brickIcon} and 2 {workerIcon} here during the Cleanup phase. Take them back during the next Production phase'
            ),
        ];
    }
}
