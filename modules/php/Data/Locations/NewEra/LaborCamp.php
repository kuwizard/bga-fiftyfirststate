<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class LaborCamp extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_LABOR_CAMP;
        $this->name = clienttranslate("Labor Camp");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_WORKER, RESOURCE_WORKER],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 2 {workerIcon}'),
        ];
    }
}
