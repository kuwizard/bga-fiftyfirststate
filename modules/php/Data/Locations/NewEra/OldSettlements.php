<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class OldSettlements extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OLD_SETTLEMENTS;
        $this->name = clienttranslate("Old Settlements");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_IRON];
        $this->icons = [ICON_IRON, ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_IRON],
            [RESOURCE_WORKER],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {ironIcon} to gain 1 {workerIcon}'),
        ];
    }
}
