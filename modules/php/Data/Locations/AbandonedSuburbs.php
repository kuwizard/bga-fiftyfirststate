<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class AbandonedSuburbs extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ABANDONED_SUBURBS;
        $this->name = clienttranslate("Abandoned Suburbs");
        $this->distance = 3;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_VP];
        $this->icons = [ICON_BRICK];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_BRICK, RESOURCE_BRICK],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 2 {brickIcon} to gain 2 {scoreIcon}'),
        ];
    }
}
