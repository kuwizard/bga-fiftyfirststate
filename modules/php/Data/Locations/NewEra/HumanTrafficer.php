<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\FeaturePassiveAbility;

class HumanTrafficer extends FeaturePassiveAbility
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HUMAN_TRAFFICER;
        $this->name = clienttranslate("Human Trafficer");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_FUEL];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Each time you make a Deal gain 1 {workerIcon}'),
        ];

        $this->passiveAbilities = [LOCATION_ACTION_DEAL => RESOURCE_WORKER];
    }
}
