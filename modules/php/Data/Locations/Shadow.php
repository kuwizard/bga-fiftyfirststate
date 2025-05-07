<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Shadow extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SHADOW;
        $this->name = clienttranslate("Shadow");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_ARROW_RED],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {arrowRedIcon} to gain 1 {scoreIcon}'),
        ];
    }
}
