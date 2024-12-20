<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Feature;

class MesmerizersDwelling extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MESMERIZERS_DWELLING;
        $this->name = clienttranslate("Mesmerizers' Dwelling");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_GUN, RESOURCE_VP];
        $this->deals = [RESOURCE_VP];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => 'Each time you Raze gain 1 {scoreIcon}',
        ];
    }
}
