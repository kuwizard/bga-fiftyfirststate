<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;
use STATE\Models\Player;

class Refinery extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_REFINERY;
        $this->name = clienttranslate("Refinery");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_FUEL];
        $this->icons = [ICON_FUEL, ICON_VP];
        $this->deals = [RESOURCE_FUEL];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                '1 {scoreIcon} for each {fuelAcon} in your State. Max. 5'
            ),
        ];
    }

    public function getBuildingBonus(Player $player = null): array
    {
        return $this->getVPForEachIcon($player, ICON_FUEL);
    }
}
