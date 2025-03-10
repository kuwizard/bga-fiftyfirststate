<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class GasolineTower extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GASOLINE_TOWER;
        $this->name = clienttranslate("Gasoline Tower");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_FUEL];
        $this->icons = [ICON_WORKER, ICON_FUEL];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_FUEL],
            [RESOURCE_WORKER],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {fuelIcon} to gain 1 {workerIcon}'),
        ];
    }
}
