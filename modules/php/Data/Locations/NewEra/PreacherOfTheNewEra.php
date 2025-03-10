<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class PreacherOfTheNewEra extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PREACHER_OF_THE_NEW_ERA;
        $this->name = clienttranslate("Preacher Of The New Era");
        $this->distance = 2;
        $this->spoils = [RESOURCE_GUN, RESOURCE_ARROW_RED, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->copies = 1;
        $this->activationsMax = 2;
        $this->action = new Act(
            [RESOURCE_ARROW_BLUE],
            [RESOURCE_ARROW_RED],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {arrowBlueIcon} to gain 1 {arrowRedIcon}'),
        ];
    }
}
