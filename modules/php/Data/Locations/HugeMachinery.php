<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class HugeMachinery extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HUGE_MACHINERY;
        $this->name = clienttranslate("Huge Machinery");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY];
        $this->icons = [ICON_IRON, ICON_ARROW];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_IRON],
            [RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY, RESOURCE_ARROW_GREY],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 1 {ironIcon} to gain 3 {arrowGreyIcon}'),
        ];
    }
}
