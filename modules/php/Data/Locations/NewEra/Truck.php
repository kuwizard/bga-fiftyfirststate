<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\FeaturePassiveAbility;

class Truck extends FeaturePassiveAbility
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_TRUCK;
        $this->name = clienttranslate("Truck");
        $this->distance = 1;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO];
        $this->icons = [ICON_ARROW, ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Each time you Raze or make a Deal gain 1 {ammoIcon}'),
        ];

        $this->passiveAbilities = [
            LOCATION_ACTION_DEAL => RESOURCE_AMMO,
            LOCATION_ACTION_RAZE => RESOURCE_AMMO,
        ];
    }
}
