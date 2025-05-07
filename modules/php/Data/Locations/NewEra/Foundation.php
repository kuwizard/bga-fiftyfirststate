<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageSingle;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionSingle;

class Foundation extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_FOUNDATION;
        $this->name = clienttranslate("Foundation");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY];
        $this->icons = [ICON_VOID];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;

        $this->resourcesOptions = [
            new ResourceStorageOptionSingle(RESOURCE_DEVELOPMENT, 1),
        ];
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 1 {develIcon} on this Location. You may spend it during your turn. Do not discard it during the Cleanup phase'
            ),
        ];
    }
}
