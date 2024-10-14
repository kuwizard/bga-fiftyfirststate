<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class School extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SCHOOL;
        $this->name = clienttranslate("School");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_WORKER, ICON_AMMO];
        $this->deals = [RESOURCE_WORKER];
        $this->product = [RESOURCE_WORKER];
        $this->buildingBonus = [RESOURCE_WORKER];
        $this->copies = 3;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {workerIcon}',
            TEXT_BONUS_DESCRIPTION => '1 {workerIcon}',
        ];
    }
}
