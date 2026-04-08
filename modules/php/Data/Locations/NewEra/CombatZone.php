<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class CombatZone extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_COMBAT_ZONE;
        $this->name = clienttranslate("Combat Zone");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_GUN, RESOURCE_GUN],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 2 {gunIcon}'),
        ];
    }
}
