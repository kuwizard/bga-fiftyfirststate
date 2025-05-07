<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Production;

class Quarry extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_QUARRY;
        $this->name = clienttranslate("Quarry");
        $this->distance = 1;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK, ICON_AMMO];
        $this->deals = [RESOURCE_BRICK];
        $this->product = [RESOURCE_BRICK];
        $this->isOpen = true;
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {brickIcon}',
        ];
    }
}
