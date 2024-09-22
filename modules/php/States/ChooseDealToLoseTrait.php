<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Managers\Players;

trait ChooseDealToLoseTrait
{
    public function argChooseDealToLose()
    {
        $resourcesOnDeals = Players::getActive()->getDeals();
        return ResourcesHelper::getResourceNames(array_values(array_unique($resourcesOnDeals)));
    }

    public function actChooseDeal(string $resourceName)
    {
        $player = Players::getActive();
        $discarded = Locations::discardByDeal(ResourcesHelper::getResourceType($resourceName), $player->getId());
        Notifications::dealDiscarded($player, $discarded, Locations::countInLocation(LOCATION_DISCARD), $resourceName);
        Stack::finishState();
    }
}
