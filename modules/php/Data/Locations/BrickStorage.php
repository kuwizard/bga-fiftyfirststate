<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageSingle;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionSingle;

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

        $this->resourcesOptions = [
            new ResourceStorageOptionSingle(RESOURCE_BRICK, 3),
        ];
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 3 {brickIcon} on this Location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}