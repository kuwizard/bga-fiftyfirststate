<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class Convoy extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CONVOY;
        $this->name = clienttranslate("Convoy");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE];
        $this->icons = [ICON_FUEL, ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_BLUE];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_FUEL],
            [RESOURCE_ARROW_BLUE],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {fuelIcon} to gain 1 {arrowBlueIcon}'),
        ];
    }
}
