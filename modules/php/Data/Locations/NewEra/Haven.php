<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Production;

class Haven extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HAVEN;
        $this->name = clienttranslate("Haven");
        $this->distance = 2;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON, RESOURCE_AMMO];
        $this->icons = [ICON_IRON, ICON_AMMO];
        $this->deals = [RESOURCE_IRON];
        $this->product = [RESOURCE_IRON, RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {ironIcon} and 1 {ammoIcon}'),
        ];
    }
}
