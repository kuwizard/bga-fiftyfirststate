<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Production;

class TheIronGang extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_THE_IRON_GANG;
        $this->name = clienttranslate("The Iron Gang");
        $this->distance = 1;
        $this->spoils = [RESOURCE_GUN, RESOURCE_WORKER];
        $this->icons = [ICON_GUN, ICON_ARROW];
        $this->deals = [RESOURCE_GUN];
        $this->product = [RESOURCE_ARROW_RED];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {arrowRedIcon}',
        ];
    }
}
