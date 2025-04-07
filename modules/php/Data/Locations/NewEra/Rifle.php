<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\FeaturePassiveAbility;

class Rifle extends FeaturePassiveAbility
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RIFLE;
        $this->name = clienttranslate("Rifle");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD];
        $this->icons = [ICON_CARD, ICON_GUN];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Each time you Raze gain 1 {cardIcon}'),
        ];

        $this->passiveAbilities = [LOCATION_ACTION_RAZE => RESOURCE_CARD];
    }
}
