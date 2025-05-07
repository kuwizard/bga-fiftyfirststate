<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Preferences;
use STATE\Managers\Factions;
use STATE\Managers\Players;
use \Bga\GameFramework\Actions\CheckAction;

trait ChooseFactionTrait
{
    public function argChooseFaction()
    {
        $preferences = Preferences::getPreferencesAll();
        return ['prodActions' => Factions::getAllProductionsAndActionsUI(), '_private' => $preferences];
    }

    public function actChooseFactionsPreferences(string $factions, string $sides)
    {
        $this->savePreferencesAndMoveOn(explode(';', $factions), explode(';', $sides));
    }

    #[CheckAction(false)]
    public function actChangedMind()
    {
        $this->gamestate->checkPossibleAction('actChangedMind');
        $this->gamestate->setPlayersMultiactive([Players::getCurrentId()], '');
    }

    public function actIDontCare()
    {
        $this->savePreferencesAndMoveOn([0, 0, 0, 0], [0, 0, 0, 0]);
    }

    private function savePreferencesAndMoveOn(array $factions, array $sides): void
    {
        $pId = Players::getCurrentId();
        Preferences::setPreferences($pId, $factions, $sides);
        $thisPlayerIsTheLastOne = count($this->gamestate->getActivePlayerList()) === 1
            && (int) $this->gamestate->getActivePlayerList()[0] === $pId;
        if ($thisPlayerIsTheLastOne) {
            Players::assignNewPreferredColorsToPlayers();
            Notifications::applyFactions(Players::getAllFactions());
            Players::giveEachPlayerCardsSetup();
            Notifications::deckChanged();
            Factions::setupNewGame(Players::getAll()->toArray());
        }
        $this->gamestate->setPlayerNonMultiactive($pId, '');
    }
}
