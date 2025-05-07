<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class RadioactiveFuel extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RADIOACTIVE_FUEL;
        $this->name = clienttranslate("Radioactive Fuel");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_VP];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_FUEL],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 1 {fuelIcon} to gain 2 {scoreIcon}'),
        ];
    }
}
