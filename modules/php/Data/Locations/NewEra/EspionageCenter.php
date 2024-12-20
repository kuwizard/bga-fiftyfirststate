<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Feature;

class EspionageCenter extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ESPIONAGE_CENTER;
        $this->name = clienttranslate("Espionage Center");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD, RESOURCE_VP];
        $this->icons = [ICON_FUEL, ICON_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => 'Each time you make a Deal gain 1 {cardIcon}',
        ];
    }
}
