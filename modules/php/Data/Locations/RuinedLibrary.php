<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageMultiple;
use STATE\Models\ResourceStorageOption;

class RuinedLibrary extends FeatureStorageMultiple
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RUINED_LIBRARY;
        $this->name = clienttranslate("Ruined Library");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;

        $this->resourcesOptions = [
            new ResourceStorageOption([RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK], 3),
        ];
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate(
                'You may store up to 3 {gunIcon} / {fuelIcon} / {ironIcon} / {brickIcon} here during the Cleanup phase. Take them back during the next Production phase'
            ),
        ];
    }
}
