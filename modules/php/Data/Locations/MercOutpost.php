<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class MercOutpost extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MERC_OUTPOST;
        $this->name = clienttranslate("Merc Outpost");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_AMMO, ICON_WORKER];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_ANY_OF_MAIN],
            [RESOURCE_WORKER, RESOURCE_WORKER],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate(
                'Spend 1 {gunIcon} / {fuelIcon} / {ironIcon} / {brickIcon} to gain 2 {workerIcon}'
            ),
        ];
    }
}
