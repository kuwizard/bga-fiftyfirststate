<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Feature;

class GuildsGarage extends Feature
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
            TEXT_DESCRIPTION => 'Each time you make a Deal gain 1 {cardIcon}',
        ];
    }
}
