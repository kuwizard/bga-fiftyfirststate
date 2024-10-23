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
        return [
            'locations' => Locations::getInLocation(LOCATION_LOOKOUT)->toArray(),
            'deckCount' => Locations::countInLocation(LOCATION_DECK),
        ];
    }

    public function stPhaseOneLookoutSetup()
    {
        Connections::flipForNewRound();
        Notifications::newConnections(Connections::getBothAvailable()->toArray());
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
        Locations::pickForLocation($amount, LOCATION_DECK, LOCATION_LOOKOUT);
        Stack::finishState();
    }

    public function stPhaseOneLookoutDiscard()
    {
        $leftoverLocations = Locations::getInLocation(LOCATION_LOOKOUT);
        if ($leftoverLocations->count() !== 1) {
            throw new \BgaVisibleSystemException('Incorrect leftover locations amount: ' . $leftoverLocations->count());
        }
        Locations::move($leftoverLocations->getIds(), LOCATION_DISCARD);
        Notifications::locationDiscarded(Players::getActive(), $leftoverLocations->first());
        Stack::finishState();
    }

    public function actChooseCardLookout(int $id): void
    {
        $player = Players::getActive();
        if (!in_array($id, Locations::getInLocation(LOCATION_LOOKOUT)->getIds())) {
            throw new \BgaVisibleSystemException(
                'You tried to choose a Location which is not in the list of possible ones to choose. This is probably a bug'
            );
        }
        Locations::move($id, [LOCATION_HAND, $player->getId()]);
        Notifications::locationPicked($player, Locations::get($id), 'lookout');
        Notifications::resourcesChanged($player, ['card' => $player->getHandAmount()]);
        Stack::finishState();
    }
}
