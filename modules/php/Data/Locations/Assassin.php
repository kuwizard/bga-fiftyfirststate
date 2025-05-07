<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Production;

class Assassin extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_ASSASSIN;
        $this->name = clienttranslate("Assassin");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN];
        $this->icons = [ICON_GUN, ICON_AMMO];
        $this->deals = [RESOURCE_GUN];
        $this->product = [RESOURCE_GUN];
        $this->isOpen = true;
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {gunIcon}',
        ];
    }
}
