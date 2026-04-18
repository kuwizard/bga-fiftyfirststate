<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\Location;

trait PlaceDefenceTrait
{
    public function argPlaceDefence()
    {
        $args = [];
        $locationsIds = Players::getActive()->getBoard()->filter(function (Location $location) {
            return !$location->isDefended();
        })->getIds();
        foreach ($locationsIds as $locationId) {
            $args[$locationId] = false;
        }
        return [
            'locations' => $args,
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
        ];
    }

    public function actPlaceDefence(int $id)
    {
        $player = Players::getActive();
        if (!isset($this->argPlaceDefence()['locations'][$id])) {
            throw new \BgaVisibleSystemException('Cannot place a defence token on location ' . $id);
        }
        Locations::addDefence($id);
        $player->decreaseResource(RESOURCE_DEFENCE);
        Notifications::locationDefended($player, Locations::get($id));
        Notifications::resourcesChanged($player, $player->getResourcesWithNames([RESOURCE_DEFENCE]));
        self::giveExtraTime($player->getId());
        $nextState = Globals::isActionDone() ? ST_CONFIRM_TURN_END : ST_PHASE_THREE_ACTION;
        Stack::insertOnTopAndFinish($nextState);
    }
}
