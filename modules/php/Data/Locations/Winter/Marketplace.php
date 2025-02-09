<?php

namespace STATE\Data\Locations;

use STATE\Models\Production;

class WireEntanglement extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MARKETPLACE;
        $this->name = clienttranslate("Maarketplace");
        $this->distance = 1;
//         $this->spoils = [RESOURCE_DEFENCE, RESOURCE_DEFENCE, RESOURCE_DEFENCE];
//         $this->icons = [ICON_GUN];
//         $this->deals = [RESOURCE_DEFENCE];
//         $this->product = [RESOURCE_DEFENCE];
//         $this->buildingBonus = [RESOURCE_DEFENCE];
//         $this->copies = 2; // TODO: Add this to expansionCopies
//         $this->text = [
//             ...$this->getText(),
//             TEXT_DESCRIPTION => '1 {defenceIcon}',
//             TEXT_BONUS_DESCRIPTION => '1 {defenceIcon}',
//         ];
    }
}
