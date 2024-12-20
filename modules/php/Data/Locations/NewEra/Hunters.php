<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Feature;

class Hunters extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HUNTERS;
        $this->name = clienttranslate("Hunters");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_CARD];
        $this->icons = [ICON_WORKER, ICON_GUN];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => 'Each time you Raze gain 1 {workerIcon}',
        ];
    }
}
