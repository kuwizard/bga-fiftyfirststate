<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Production;

class BrickSupplier extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BRICK_SUPPLIER;
        $this->name = clienttranslate("Brick Supplier");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK, ICON_AMMO];
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {brickIcon} for each {brickAcon} in your State. Max. 3'),
        ];
    }

    public function getProduct($player): array
    {
        $icons = $player->getBoardIcons(ICON_BRICK);
        $maxIcons = array_slice($icons, 0, 3);
        return array_fill(0, count($maxIcons), RESOURCE_BRICK);
    }
}
