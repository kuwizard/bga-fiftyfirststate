<?php

namespace STATE\States;

use STATE\Core\Stack;
use STATE\Managers\Players;
use STATE\Models\Faction;
use STATE\Models\Player;

trait PhaseTwoProductionTrait
{
    public function stPhaseTwoProduction()
    {
        $factionsNames = [
            FACTION_NEW_YORK => 'NewYork',
            FACTION_APPALACHIAN => 'Appalachian',
            FACTION_MUTANTS => 'Mutants',
            FACTION_MERCHANTS => 'Merchants',
        ];

        /** @var Player $player */
        foreach (Players::getAll() as $player) {
            $name = 'STATE\Data\Factions\\' . $factionsNames[$player->getFaction()];
            /** @var Faction $faction */
            $faction = new $name;
            $factionProd = $faction->getProduction();
            $dealsProd = $player->getDeals();
            $prodLocations = $player->getProduction();
            $player->increaseResources(array_count_values(array_merge($factionProd, $dealsProd, $prodLocations)));
        }
        Stack::finishState();
    }
}
