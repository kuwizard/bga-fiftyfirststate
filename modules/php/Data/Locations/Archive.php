<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class Archive extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ARCHIVE;
        $this->name = clienttranslate("Archive");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER, ICON_VP];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
    }
}
