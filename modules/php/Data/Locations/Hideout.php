<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Hideout extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HIDEOUT;
        $this->name = clienttranslate("Hideout");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_VP];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_DEAL],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Discard 1 of your Deals to gain 1 {scoreIcon}'),
        ];
    }
}
