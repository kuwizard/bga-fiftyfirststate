<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Production;

class NaturalShelters extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_NATURAL_SHELTERS;
        $this->name = clienttranslate("Natural Shelters");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_AMMO];
        $this->icons = [ICON_WORKER, ICON_AMMO];
        $this->deals = [RESOURCE_WORKER];
        $this->product = [RESOURCE_WORKER, RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {workerIcon} and 1 {ammoIcon}'),
        ];
    }
}
