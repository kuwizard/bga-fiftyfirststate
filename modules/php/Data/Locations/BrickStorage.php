<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageMultiple;
use STATE\Models\ResourceStorageOption;

class BrickStorage extends FeatureStorageMultiple
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BRICK_STORAGE;
        $this->name = clienttranslate("Brick Storage");
        $this->distance = 1;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;

        $this->resourcesOptions = [
            new ResourceStorageOption([RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK], 2),
            new ResourceStorageOption([RESOURCE_WORKER], 2),
        ];
    }
}
