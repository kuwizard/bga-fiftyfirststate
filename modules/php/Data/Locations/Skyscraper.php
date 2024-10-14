<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class Skyscraper extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SKYSCRAPER;
        $this->name = clienttranslate("Skyscraper");
        $this->distance = 3;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_VP];
        $this->icons = [ICON_BRICK, ICON_VP];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_BRICK, RESOURCE_BRICK],
            [RESOURCE_VP, RESOURCE_VP, RESOURCE_VP],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 2 {brickIcon} to gain 3 {scoreIcon}'),
        ];
    }
}
