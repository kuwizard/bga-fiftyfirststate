<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Location;

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
        return $args;
    }

    public function actPlaceDefence(int $id)
    {
        $player = Players::getActive();
        if (!in_array($id, array_keys($this->argPlaceDefence()))) {
            throw new \BgaVisibleSystemException('Cannot place a defence token on location ' . $id);
        }
        Locations::addDefence($id);
        $player->decreaseResource(RESOURCE_DEFENCE);
        Notifications::locationDefended($player, Locations::get($id));
        Notifications::resourcesChanged($player, $player->getResourcesWithNames([RESOURCE_DEFENCE]));
        self::giveExtraTime($player->getId());
        Stack::finishState();
    }
}
