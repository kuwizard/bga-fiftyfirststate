<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageMultiple;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionMulti;

class GangersDive extends FeatureStorageMultiple
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GANGERS_DIVE;
        $this->name = clienttranslate("Gangers' Dive");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED];
        $this->icons = [ICON_ARROW, ICON_GUN];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->copies = 1;
        $this->buildingBonus = [RESOURCE_ARROW_RED];

        $this->resourcesOptions = [new ResourceStorageOptionMulti([RESOURCE_ARROW_RED], 3)];
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => clienttranslate(
                'You may store up to 3 {arrowRedIcon} during the Cleanup phase. Take them back during the next Production phase'
            ),
            TEXT_BONUS_DESCRIPTION => '1 {arrowRedIcon}',
        ];
    }
}
