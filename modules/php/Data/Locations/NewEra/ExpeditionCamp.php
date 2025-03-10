<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Production;

class ExpeditionCamp extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_EXPEDITION_CAMP;
        $this->name = clienttranslate("Expedition Camp");
        $this->distance = 2;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_AMMO];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->product = [RESOURCE_GUN, RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {gunIcon} and 1 {ammoIcon}'),
        ];
    }
}
