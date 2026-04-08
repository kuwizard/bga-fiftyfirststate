<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class OilTrader extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OIL_TRADER;
        $this->name = clienttranslate("Oil Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_FUEL],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {fuelIcon} to gain 1 {scoreIcon}'),
        ];
    }
}
