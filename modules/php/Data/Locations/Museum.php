<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;
use STATE\Models\Player;

class Museum extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MUSEUM;
        $this->name = clienttranslate("Museum");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_CARD];
        $this->icons = [ICON_CHURCH];
        $this->deals = [RESOURCE_VP];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                '1 {scoreIcon} for each {churchAcon} in your State. Max. 5'
            ),
        ];
    }

    public function getBuildingBonus(Player $player = null): array
    {
        return $this->getVPForEachIcon($player, ICON_CHURCH);
    }
}
