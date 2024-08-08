<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Managers\Factions;
use STATE\Managers\Players;
use STATE\Models\Faction;
use STATE\Models\Player;

trait PhaseTwoProductionTrait
{
    public function stPhaseTwoProduction()
    {


        /** @var Player $player */
        foreach (Players::getAll() as $player) {
            $factionProd = $player->getFactionProduction();
            $dealsProd = $player->getDeals();
            $prodLocations = $player->getProduction();
            $combinedResources = array_count_values(array_merge($factionProd, $dealsProd, $prodLocations));
            $player->increaseResources($combinedResources);
            Notifications::resourcesChanged($player, $player->getResourcesWithNames(array_keys($combinedResources)));
            Notifications::handChanged($player);
        }
        Stack::finishState();
    }
}
