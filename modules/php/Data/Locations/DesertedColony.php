<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class DesertedColony extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_DESERTED_COLONY;
        $this->name = clienttranslate("Deserted Colony");
        $this->distance = 2;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_VP];
        $this->icons = [ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_AMMO],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 1 {ammoIcon}'),
        ];
    }
}
