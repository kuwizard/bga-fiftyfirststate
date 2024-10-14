<?php

namespace STATE\Data\Locations;

use STATE\Managers\Players;
use STATE\Models\Act;
use STATE\Models\Action;
use STATE\Models\Player;

class ThievesCaravan extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_THIEVES_CARAVAN;
        $this->name = clienttranslate("Thieves Caravan");
        $this->distance = 3;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_CARD];
        $this->icons = [ICON_AMMO, ICON_GUN];
        $this->deals = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_WORKER],
            [RESOURCE_FUEL, RESOURCE_GUN, RESOURCE_IRON, RESOURCE_BRICK],
            ACTION_TYPE_STEAL_ANOTHER_PLAYER
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate(
                'Spend 2 {workerIcon} to take 1 {gunIcon} / {fuelIcon} / {ironIcon} / {brickIcon} from another player'
            ),
        ];
    }

    public function isActivatable(): bool
    {
        $otherPlayers = Players::getAll(Players::getActiveId());
        $playersWithResources = $otherPlayers->filter(function (Player $player) {
            return array_sum(array_values($player->getResourcesWithNames($this->action->getBonus()))) > 0;
        });
        return parent::isActivatable() && !$playersWithResources->empty();
    }
}
