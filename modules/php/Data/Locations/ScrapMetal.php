<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageSingle;

class ScrapMetal extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SCRAP_METAL;
        $this->name = clienttranslate("Scrap Metal");
        $this->distance = 1;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON];
        $this->icons = [ICON_IRON];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;

        $this->resourceType = RESOURCE_IRON;
        $this->resourceLimit = 3;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 3 {ironIcon} on this location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}
