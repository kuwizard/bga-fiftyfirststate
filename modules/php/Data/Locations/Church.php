<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;

class Church extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_CHURCH;
        $this->name = clienttranslate("Church");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->buildingBonus = [RESOURCE_VP, RESOURCE_VP];
        $this->copies = 2;
        $this->expansionCopies = [
            NEW_ERA => 1,
        ];
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '2 {scoreIcon}',
        ];
    }
}
