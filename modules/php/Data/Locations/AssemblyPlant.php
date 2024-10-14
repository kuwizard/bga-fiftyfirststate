<?php

namespace STATE\Data\Locations;

use STATE\Models\Act;
use STATE\Models\Action;

class AssemblyPlant extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ASSEMBLY_PLANT;
        $this->name = clienttranslate("Assembly Plant");
        $this->distance = 3;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_IRON, RESOURCE_VP];
        $this->icons = [ICON_IRON, ICON_VP];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_IRON, RESOURCE_IRON],
            [RESOURCE_VP, RESOURCE_VP, RESOURCE_VP],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 2 {ironIcon} to gain 3 {scoreIcon}'),
        ];
    }
}
