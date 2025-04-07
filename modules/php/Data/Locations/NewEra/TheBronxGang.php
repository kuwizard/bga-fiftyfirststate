<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class TheBronxGang extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_THE_BRONX_GANG;
        $this->name = clienttranslate("The Bronx Gang");
        $this->distance = 2;
        $this->spoils = [RESOURCE_GUN, RESOURCE_ARROW_RED, RESOURCE_CARD];
        $this->icons = [ICON_ARROW, ICON_GUN];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_GUN],
            [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED, RESOURCE_ARROW_RED],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 1 {gunIcon} to gain 3 {arrowRedIcon}'),
        ];
    }
}
