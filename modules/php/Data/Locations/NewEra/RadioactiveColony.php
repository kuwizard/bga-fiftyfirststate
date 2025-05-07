<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageSingle;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionSingle;

class RadioactiveColony extends FeatureStorageSingle
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RADIOACTIVE_COLONY;
        $this->name = clienttranslate("Radioactive Colony");
        $this->distance = 2;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_WORKER];
        $this->icons = [ICON_VOID];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;

        $this->resourcesOptions = [
            new ResourceStorageOptionSingle(RESOURCE_WORKER, 1),
            new ResourceStorageOptionSingle(RESOURCE_AMMO, 2),
            new ResourceStorageOptionSingle(RESOURCE_ARROW_BLUE, 1),
            new ResourceStorageOptionSingle(RESOURCE_ARROW_RED, 1),
        ];
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                'Place 1 {workerIcon}, 2 {ammoIcon}, 1 {arrowBlueIcon}, and 1 {arrowRedIcon} on this Location. You may spend them during your turn. Do not discard them during the Cleanup phase'
            ),
        ];
    }
}
