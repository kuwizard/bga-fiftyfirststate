<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class GasolineCultist extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GASOLINE_CULTIST;
        $this->name = clienttranslate("Gasoline Cultist");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_BLUE, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_ARROW_BLUE],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {arrowBlueIcon} to gain 1 {scoreIcon}'),
        ];
    }
}
