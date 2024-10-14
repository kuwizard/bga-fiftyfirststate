<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageSingle;

class CityGuards extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CITY_GUARDS;
        $this->name = clienttranslate("City Guards");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;

        $this->resourceType = RESOURCE_GUN;
        $this->resourceLimit = 3;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 3 {gunIcon} on this location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}
