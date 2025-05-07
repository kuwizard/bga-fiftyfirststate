<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageSingle;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionSingle;

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

        $this->resourcesOptions = [
            new ResourceStorageOptionSingle(RESOURCE_GUN, 3),
        ];
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 3 {gunIcon} on this Location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}
