<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\FeaturePassiveAbility;

class GuildsGarage extends FeaturePassiveAbility
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GUILDS_GARAGE;
        $this->name = clienttranslate("Guild's Garage");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD];
        $this->icons = [ICON_FUEL, ICON_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Each time you make a Deal gain 1 {cardIcon}'),
        ];

        $this->passiveAbilities = [LOCATION_ACTION_DEAL => RESOURCE_CARD];
    }
}
