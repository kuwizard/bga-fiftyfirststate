<?php

namespace STATE\Data\Cards;

use STATE\Models\Action;
use STATE\Models\ActionCard;

class AssemblyPlant extends ActionCard
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
        $this->action = new Action(
            [RESOURCE_WORKER, RESOURCE_IRON, RESOURCE_IRON],
            [RESOURCE_VP, RESOURCE_VP, RESOURCE_VP],
        );
    }
}
