<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class Pub extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PUB;
        $this->name = clienttranslate("Pub");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD, RESOURCE_CARD];
        $this->icons = [ICON_CARD];
        $this->deals = [RESOURCE_CARD];
        $this->product = [RESOURCE_CARD];
        $this->isOpen = true;
        $this->buildingBonus = [RESOURCE_CARD];
        $this->copies = 2;
        $this->expansionCopies = [
            WINTER => 1,
        ];
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {cardIcon}',
            TEXT_BONUS_DESCRIPTION => '1 {cardIcon}',
        ];
    }
}
