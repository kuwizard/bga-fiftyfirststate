<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class GunShop extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_GUN_SHOP;
        $this->name = clienttranslate("Gun Shop");
        $this->distance = 3;
        $this->spoils = [RESOURCE_GUN, RESOURCE_GUN, RESOURCE_GUN, RESOURCE_VP];
        $this->icons = [ICON_GUN, ICON_VP];
        $this->deals = [RESOURCE_GUN];
        $this->copies = 2;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_GUN, RESOURCE_GUN],
            [RESOURCE_VP, RESOURCE_VP, RESOURCE_VP],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} and 2 {gunIcon} to gain 3 {scoreIcon}'),
        ];
    }
}
