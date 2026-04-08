<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class RubbleTrader extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RUBBLE_TRADER;
        $this->name = clienttranslate("Rubble Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_CARD];
        $this->icons = [ICON_BRICK, ICON_VP];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_BRICK],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {brickIcon} to gain 1 {scoreIcon}'),
        ];
    }
}
