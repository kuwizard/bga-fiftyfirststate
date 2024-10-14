<?php

namespace STATE\Data\Locations;

use STATE\Models\Feature;
use STATE\Models\Player;

class Factory extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_FACTORY;
        $this->name = clienttranslate("Factory");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_IRON];
        $this->icons = [ICON_IRON, ICON_VP];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                '1 {scoreIcon} for each {ironAcon} in your State. Max. 5'
            ),
        ];
    }

    public function getBuildingBonus(Player $player = null): array
    {
        return $this->getVPForEachIcon($player, ICON_IRON);
    }
}
