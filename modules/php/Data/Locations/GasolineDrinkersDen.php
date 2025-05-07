<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class GasolineDrinkersDen extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GASOLINE_DEN;
        $this->name = clienttranslate("Gasoline Drinkers Den");
        $this->distance = 3;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_FUEL, RESOURCE_VP];
        $this->icons = [ICON_FUEL];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_FUEL, RESOURCE_FUEL],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 2 {fuelIcon} to gain 2 {scoreIcon}'),
        ];
    }
}
