<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Shelter extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SHELTER;
        $this->name = clienttranslate("Shelter");
        $this->distance = 3;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_IRON, RESOURCE_VP];
        $this->icons = [ICON_IRON];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_IRON, RESOURCE_IRON],
            [RESOURCE_VP, RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 2 {ironIcon} to gain 2 {scoreIcon}'),
        ];
    }
}
