<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Stack;
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
            $nextPlayer = Players::getFirstFirstPlayerId();
        } else {
            $nextPlayer = Players::getNextId($firstPlayer);
        }
        Globals::setFirstPlayerId($nextPlayer);

        $stack = [
            ST_PHASE_ONE_LOOKOUT_SETUP,
            ST_PHASE_TWO_PRODUCTION,
            ST_PHASE_THREE_ACTION,
            ST_PHASE_FOUR_CLEANUP,
            ST_NEXT_ROUND,
        ];

        if (Globals::isLastRound()) {
            $this->setTieBreakersAndVPForLocations();
            $stack = [ST_END_GAME];
        } else {
            $this->gamestate->changeActivePlayer($nextPlayer);
            self::giveExtraTime($nextPlayer);
        }
        Stack::setup($stack);
        Stack::finishState();
    }

    private function setTieBreakersAndVPForLocations()
    {
        /** @var Player $player */
        foreach (Players::getAll() as $player) {
            $resourcesCount = $player->getTotalResourcesCount();
            $locationsCount = $player->getBoard()->count();
            $player->setTieBreaker(intval(strval($resourcesCount) . strval($locationsCount)));
            $totalAmount = $player->increaseResource(RESOURCE_VP, $locationsCount);
            Notifications::endOfGameVPGained($player, $locationsCount, $totalAmount);
        }
    }
}
