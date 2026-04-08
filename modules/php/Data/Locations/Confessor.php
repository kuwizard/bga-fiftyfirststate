<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Confessor extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CONFESSOR;
        $this->name = clienttranslate("Confessor");
        $this->distance = 3;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_GUN, RESOURCE_VP];
        $this->icons = [ICON_GUN];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_GUN, RESOURCE_GUN],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 2 {gunIcon} to gain 2 {scoreIcon}'),
        ];
    }
}
