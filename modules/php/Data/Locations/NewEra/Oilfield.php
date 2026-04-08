<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Oilfield extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OILFIELD;
        $this->name = clienttranslate("Oilfield");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_FUEL, RESOURCE_FUEL],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 2 {fuelIcon}'),
        ];
    }
}
