<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class OldCinema extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_OLD_CINEMA;
        $this->name = clienttranslate("Old Cinema");
        $this->distance = 1;
        $this->spoils = [RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->buildingBonus = [RESOURCE_VP];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => '1 {scoreIcon}',
        ];
    }
}
