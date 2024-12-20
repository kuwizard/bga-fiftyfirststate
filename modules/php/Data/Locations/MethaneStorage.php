<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageSingle;

class MethaneStorage extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_METHANE_STORAGE;
        $this->name = clienttranslate("Methane Storage");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;

        $this->resourceType = RESOURCE_FUEL;
        $this->resourceLimit = 3;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 3 {fuelIcon} on this Location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}
