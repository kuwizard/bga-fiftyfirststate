<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageSingle;

class Camp extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CAMP;
        $this->name = clienttranslate("Camp");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;

        $this->resourceType = RESOURCE_WORKER;
        $this->resourceLimit = 3;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 3 {workerIcon} on this location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}
