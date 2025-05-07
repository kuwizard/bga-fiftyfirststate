<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\FeaturePassiveAbility;

class MesmerizersDwelling extends FeaturePassiveAbility
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MESMERIZERS_DWELLING;
        $this->name = clienttranslate("Mesmerizers' Dwelling");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_VP];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Each time you Raze gain 1 {scoreIcon}'),
        ];

        $this->passiveAbilities = [LOCATION_ACTION_RAZE => RESOURCE_VP];
    }
}
