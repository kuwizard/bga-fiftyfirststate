<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

class ProductionManager extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_PRODUCTION_MANAGER;
        $this->name = clienttranslate("Production Manager");
        $this->distance = 2;
        $this->spoils = [RESOURCE_CARD, RESOURCE_CARD, RESOURCE_AMMO];
        $this->icons = [ICON_RESPIRATOR, ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_WORKER],
            [],
            ACTION_TYPE_ACTIVATE_PRODUCTION
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 2 {workerIcon} to activate one of your Production Locations'),
        ];
    }

    public function isActivatable(): bool
    {
        return parent::isActivatable() && !empty(Players::getActive()->getProductionLocations()->getIds());
    }
}
