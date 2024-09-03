<?php

namespace STATE\States;

use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Managers\Connections;
use STATE\Managers\Locations;
use STATE\Managers\Players;

trait PhaseOneLookoutTrait
{
    public function argPhaseOneLookoutChoose()
    {
        return Locations::getInLocation(LOCATION_LOOKOUT)->toArray();
    }

    public function stPhaseOneLookoutSetup()
    {
        Connections::flipForNewRound();
        $firstPlayer = Players::get(Globals::getFirstPlayerId());
        $players = Players::getPlayerIdsSortedByNo($firstPlayer);
        Stack::insertOnTop(ST_PHASE_ONE_LOOKOUT_DISCARD);
        foreach (($players) as $pId) {
            Stack::insertOnTop(ST_PHASE_ONE_LOOKOUT_CHOOSE, ['pId' => $pId]);
        }
        Stack::insertOnTop(ST_PHASE_ONE_LOOKOUT_DRAW, ['amount' => count($players) + 1]);
        Stack::insertOnTop(ST_PHASE_ONE_LOOKOUT_DISCARD);
        foreach (array_reverse($players) as $pId) {
            Stack::insertOnTop(ST_PHASE_ONE_LOOKOUT_CHOOSE, ['pId' => $pId]);
        }
        Stack::insertOnTopAndFinish(ST_PHASE_ONE_LOOKOUT_DRAW, ['amount' => count($players) + 1]);
    }

    public function stPhaseOneLookoutDraw()
    {
        $amount = Stack::getCtx()['amount'];
        Locations::move(Locations::getTopOf(LOCATION_DECK, $amount)->getIds(), LOCATION_LOOKOUT);
        Stack::finishState();
    }

    public function stPhaseOneLookoutDiscard()
    {
        $leftoverLocations = Locations::getInLocation(LOCATION_LOOKOUT)->getIds();
        if (count($leftoverLocations) !== 1) {
            throw new BgaVisibleSystemException('Incorrect leftover locations amount: ' . count($leftoverLocations));
        }
        Locations::move($leftoverLocations, LOCATION_DISCARD);
        Notifications::locationDiscarded(
            Players::getActive(),
            $leftoverLocations[0],
            Locations::countInLocation(LOCATION_DISCARD)
        );
        Stack::finishState();
    }

    public function actChooseCardLookout($id)
    {
        self::checkAction('actChooseCardLookout');
        $player = Players::getActive();
        Locations::move($id, [LOCATION_HAND, $player->getId()]);
        Notifications::handChanged($player);
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
        Stack::finishState();
    }
}
