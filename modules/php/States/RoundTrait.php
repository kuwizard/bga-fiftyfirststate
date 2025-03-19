<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Managers\Factions;
use STATE\Managers\Players;
use STATE\Models\Player;

trait RoundTrait
{
    public function argNoNotify()
    {
        return [
            '_no_notify' => true,
        ];
    }

    public function stNextRound()
    {
        $firstPlayer = Globals::getFirstPlayerId();
        if ($firstPlayer === 0) {
            $nextPlayerId = Players::getFirstFirstPlayerId();
            // If for some reason Factions never called setupNewGame(), let's call it now (it might happen in debug only!)
            $playerFaction = Players::get($nextPlayerId)->getFaction();
            if (empty(Factions::getAllForFaction($playerFaction))) {
                Factions::setupNewGame(Players::getAll()->toArray());
            }
        } else {
            $nextPlayerId = Players::getNextId($firstPlayer);
        }
        Globals::setFirstPlayerId($nextPlayerId);
        Notifications::firstPlayerChanged(Players::get($nextPlayerId));

        $stack = [
            ST_PHASE_ONE_LOOKOUT_SETUP,
            ST_PHASE_TWO_PRODUCTION,
            ST_PHASE_THREE_ACTION,
            ST_PHASE_FOUR_CLEANUP,
            ST_NEXT_ROUND,
        ];

        if (Globals::isLastRound()) {
            $this->setTieBreakersAndVPForLocations();
            Globals::setLastRound(false);
            Notifications::removeLastRound();
            $stack = [ST_END_GAME];
        } else {
            $this->gamestate->changeActivePlayer($nextPlayerId);
            self::giveExtraTime($nextPlayerId);
        }
        Stack::setup($stack);
        Stack::finishState();
    }

    private function setTieBreakersAndVPForLocations()
    {
        /** @var Player $player */
        foreach (Players::getAll() as $player) {
            $resourcesCount = $player->getTotalResourcesCount();
            $resourcesCount = $resourcesCount + array_sum(array_values($this->getResourcesFromCards($player)));
            $locationsCount = $player->getBoard()->count();
            $locationsCount = strval($locationsCount);
            if (strlen($locationsCount) === 1) {
                $locationsCount = '0' . $locationsCount;
            }
            $player->setTieBreaker(intval($resourcesCount . $locationsCount));
            $totalAmount = $player->increaseResource(RESOURCE_VP, $locationsCount);
            Notifications::endOfGameVPGained($player, $locationsCount, $totalAmount);
        }
    }
}
