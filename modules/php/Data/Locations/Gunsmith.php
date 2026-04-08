<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Gunsmith extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GUNSMITH;
        $this->name = clienttranslate("Gunsmith");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED];
        $this->icons = [ICON_GUN, ICON_ARROW];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_GUN],
            [RESOURCE_ARROW_RED],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {gunIcon} to gain 1 {arrowRedIcon}'),
        ];
    }
}
