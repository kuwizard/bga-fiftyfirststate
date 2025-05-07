<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Feature;
use Bga\Games\Fiftyfirststate\Models\Player;

class MurderersPub extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_MURDERERS_PUB;
        $this->name = clienttranslate("Murderers' Pub");
        $this->distance = 2;
        $this->spoils = [RESOURCE_VP, RESOURCE_VP, RESOURCE_GUN];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => '-',
            TEXT_BONUS_DESCRIPTION => clienttranslate(
                '1 {scoreIcon} for each {gunAcon} in your State. Max. 5'
            ),
        ];
    }

    public function getBuildingBonus(Player $player = null): array
    {
        return $this->getVPForEachIcon($player, ICON_GUN);
    }
}
