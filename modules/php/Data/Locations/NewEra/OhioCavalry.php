<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class OhioCavalry extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OHIO_CAVALRY;
        $this->name = clienttranslate("Ohio Cavalry");
        $this->distance = 2;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_ARROW_BLUE, RESOURCE_WORKER];
        $this->icons = [ICON_FUEL, ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_BLUE];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_FUEL],
            [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 1 {fuelIcon} to gain 3 {arrowBlueIcon}'),
        ];
    }
}
