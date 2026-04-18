<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Preferences;
use Bga\Games\Fiftyfirststate\Core\Stats;
use Bga\Games\Fiftyfirststate\Managers\Factions;
use Bga\Games\Fiftyfirststate\Managers\Players;
use \Bga\GameFramework\Actions\CheckAction;

trait ChooseFactionTrait
{
    public function argChooseFaction()
    {
        $preferences = Preferences::getPreferencesAll();
        return [
            'prodActions' => Factions::getAllProductionsAndActionsUI(),
            '_private' => $preferences,
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
        ];
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
            Notifications::applyFactions(Players::getAllFactionsUI());
            Players::giveEachPlayerCardsSetup();
            Notifications::deckChanged();
            Factions::setupNewGame(Players::getAll()->toArray());
            Stats::applyFactions(Players::getAllFactions());
        }
        $this->gamestate->setPlayerNonMultiactive($pId, '');
    }
}
