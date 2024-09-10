<?php

namespace STATE\Data\Locations;

use STATE\Models\FeatureStorageSingle;

class BrickStorage extends FeatureStorageSingle
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

        $this->resourceType = RESOURCE_BRICK;
        $this->resourceLimit = 3;
    }
}
