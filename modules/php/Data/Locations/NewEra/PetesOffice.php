<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\FeaturePassiveAbility;

class PetesOffice extends FeaturePassiveAbility
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PETES_OFFICE;
        $this->name = clienttranslate("Pete's Office");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_VP, ICON_FUEL];
        $this->deals = [RESOURCE_VP];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => 'Each time you make a Deal gain 1 {scoreIcon}',
        ];

        $this->passiveAbilities = [LOCATION_ACTION_DEAL => RESOURCE_VP];
    }
}
