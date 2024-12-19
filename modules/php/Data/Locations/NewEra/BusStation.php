<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Feature;
use STATE\Models\Player;

class BusStation extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BUS_STATION;
        $this->name = clienttranslate("Bus Station");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_FUEL];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => 'Each time you make a Deal gain 1 {workerIcon}',
        ];
    }
}
