<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class ScrapTrader extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SCRAP_TRADER;
        $this->name = clienttranslate("Scrap Trader");
        $this->distance = 2;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_CARD];
        $this->icons = [ICON_IRON, ICON_VP];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_IRON],
            [RESOURCE_VP],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {ironIcon} to gain 1 {scoreIcon}'),
        ];
    }
}
