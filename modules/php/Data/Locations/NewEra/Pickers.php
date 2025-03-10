<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class Pickers extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PICKERS;
        $this->name = clienttranslate("Pickers");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER, ICON_ARROW];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_ARROW_BLUE],
            [RESOURCE_WORKER],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {arrowBlueIcon} to gain 1 {workerIcon}'),
        ];
    }
}
