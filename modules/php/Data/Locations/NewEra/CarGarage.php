<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Production;

class CarGarage extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CAR_GARAGE;
        $this->name = clienttranslate("Car Garage");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_GUN, RESOURCE_WORKER];
        $this->icons = [ICON_RESPIRATOR, ICON_GUN];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->product = [RESOURCE_DEFENCE];
        $this->isOpen = true;
        $this->buildingBonus = [RESOURCE_DEFENCE];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '1 {defenceIcon}',
            TEXT_BONUS_DESCRIPTION => '1 {defenceIcon}',
        ];
    }
}
