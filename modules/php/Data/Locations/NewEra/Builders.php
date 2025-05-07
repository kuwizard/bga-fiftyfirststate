<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class Builders extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BUILDERS;
        $this->name = clienttranslate("Builders");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK, ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_BRICK],
            [RESOURCE_WORKER],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {brickIcon} to gain 1 {workerIcon}'),
        ];
    }
}
