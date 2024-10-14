<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Crossroads extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CROSSROADS;
        $this->name = clienttranslate("Crossroads");
        $this->distance = 1;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_CARD];
        $this->buildingBonus = [RESOURCE_CARD, RESOURCE_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => '2 {cardIcon}',
        ];
    }
}
