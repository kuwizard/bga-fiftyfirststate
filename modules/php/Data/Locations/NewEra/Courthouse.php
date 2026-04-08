<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Production;

class Courthouse extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_COURTHOUSE;
        $this->name = clienttranslate("Courthouse");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_AMMO];
        $this->icons = [ICON_BRICK, ICON_AMMO];
        $this->deals = [RESOURCE_BRICK];
        $this->product = [RESOURCE_BRICK, RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {brickIcon} and 1 {ammoIcon}'),
        ];
    }
}
