<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class BrickVillage extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BRICK_VILLAGE;
        $this->name = clienttranslate("Brick Village");
        $this->distance = 1;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_BRICK, RESOURCE_BRICK],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 2 {brickIcon}'),
        ];
    }
}
