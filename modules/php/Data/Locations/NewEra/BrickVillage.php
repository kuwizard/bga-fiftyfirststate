<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

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
