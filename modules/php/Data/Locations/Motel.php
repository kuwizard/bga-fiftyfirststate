<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Motel extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MOTEL;
        $this->name = clienttranslate("Motel");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD, RESOURCE_VP];
        $this->icons = [ICON_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_CARD],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 1 {cardIcon}'),
        ];
    }
}
