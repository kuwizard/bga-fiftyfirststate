<?php

namespace STATE\States;

use STATE\Managers\Players;

trait DaySetupTrait
{
    public function argRoundSetup()
    {
        return [
            // We return 'next' because at the moment arg is called, st was not yet called and haven't switched player yet
            'active_player_id' => Players::getNextAfterGuesser()->getId(),
        ];
    }

    /*
     * stRoundSetup: called before starting a new round
     */
    public function stRoundSetup()
    {
//    $this->gamestate->jumpToState();
    }
}
