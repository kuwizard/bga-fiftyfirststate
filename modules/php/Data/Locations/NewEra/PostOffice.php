<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class PostOffice extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_POST_OFFICE;
        $this->name = clienttranslate("Post Office");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE, RESOURCE_CARD];
        $this->icons = [ICON_ARROW, ICON_FUEL];
        $this->deals = [RESOURCE_ARROW_BLUE];
        $this->copies = 2;
        $this->activationsMax = 2;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_ARROW_BLUE],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 1 {arrowBlueIcon}'),
        ];
    }
}
