<?php

namespace Bga\Games\Fiftyfirststate;

use Bga\GameFramework\Table;
use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Preferences;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Core\Stats;
use Bga\Games\Fiftyfirststate\Managers\Connections;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;

class Game extends Table
{
    use States\RoundTrait;
    use States\DiscardCardsGameStartTrait;
    use States\PhaseOneLookoutTrait;
    use States\PhaseTwoProductionTrait;
    use States\PhaseThreeActionTrait;
    use States\PhaseFourCleanupTrait;
    use States\ChooseResourceSourceTrait;
    use States\ChooseResourceToStoreTrait;
    use States\DevelopTrait;
    use States\ChoosePlayerToStealTrait;
    use States\ActivateSecondTimeTrait;
    use States\ActivateProductionTrait;
    use States\SpecificLocationsActionsTrait;
    use States\ConfirmTurnEndTrait;
    use States\ChooseFactionTrait;
    use States\PlaceDefenceTrait;

    public static $instance = null;

    function __construct()
    {
        parent::__construct();
        self::$instance = $this;
        self::initGameStateLabels([
            'expansion' => 101,
        ]);

        require_once dirname(__FILE__) . "/constants.inc.php";
    }

    public static function get(): Game
    {
        return self::$instance;
    }

    /*
     * setupNewGame:
     */
    protected function setupNewGame($players, $options = [])
    {
        Players::setupNewGame($players, $options);
        Locations::setupNewGame();
        Connections::setupNewGame();
        Preferences::setupNewGame($players, $this->player_preferences);
        Globals::setupNewGame();
    }

    /*
     * getAllDatas:
     */
    public function getAllDatas(): array
    {
        $this->updateDBTableCustom();
        $currentPlayerId = Players::getCurrentId();
        return [
            'players' => Players::getUiData($currentPlayerId),
            'firstPlayerId' => Globals::getFirstPlayerId(),
            'deck' => Locations::countInLocation(LOCATION_DECK),
            'discard' => Locations::countInLocation(LOCATION_DISCARD),
            'discardLastLocation' => Locations::getTopOf(LOCATION_DISCARD),
            'connections' => [
                Connections::getTopOf(LOCATION_CONNECTIONS_RED_FLIPPED),
                Connections::getTopOf(LOCATION_CONNECTIONS_BLUE_FLIPPED),
            ],
            'lastRound' => Globals::isLastRound(),
        ];
    }

    /*
     * getGameProgression:
     */
    function getGameProgression()
    {
        $players = Players::getAll();
        $maxVP = max(
            $players->map(function ($player) {
                return $player->getScore();
            })->toArray()
        );
        if ($maxVP >= 25) {
            if ($this->gamestate->state()['name'] === 'gameEnd') {
                return 100;
            } else {
                return 99;
            }
        } else if ($maxVP === 0) {
            return match ($this->gamestate->state()['name']) {
                'discardCardsGameStart' => 0,
                'phaseOneLookoutChoose' => 1,
                default => 2,
            };
        } else {
            return (100 / 25) * $maxVP;
        }
    }

    function actChangePreference($pref, $value)
    {
//        Preferences::set($this->getCurrentPlayerId(), $pref, $value);
    }

    ///////////////////////////
    //// DEBUG FUNCTIONS //////
    ///////////////////////////

    ////////////////////////////////////
    ////////////   Zombie   ////////////
    ////////////////////////////////////
    /*
     * zombieTurn:
     *   This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     *   You can do whatever you want in order to make sure the turn of this player ends appropriately
     */
    public function zombieTurn($state, $activePlayer): void
    {
        switch ($state['name']) {
            case 'discardCardsGameStart':
                $this->gamestate->setPlayerNonMultiactive(Players::getCurrentId(), '');
                break;
            case 'phaseOneLookoutChoose':
                $lookoutLocations = Locations::getInLocation(LOCATION_LOOKOUT)->toArray();
                shuffle($lookoutLocations);
                $this->actChooseCardLookout($lookoutLocations[0]->getId());
                break;
            case 'phaseThreeAction':
                $this->actActionPass();
                break;
            default:
                Stack::finishState();
                break;
        }
    }

    /////////////////////////////////////
    //////////   DB upgrade   ///////////
    /////////////////////////////////////
    // You don't have to care about this until your game has been published on BGA.
    // Once your game is on BGA, this method is called everytime the system detects a game running with your old Database scheme.
    // In this case, if you change your Database scheme, you just have to apply the needed changes in order to
    //   update the game database and allow the game to continue to run with your new version.
    /////////////////////////////////////
    /*
     * upgradeTableDb
     *  - int $from_version : current version of this game database, in numerical form.
     *      For example, if the game was running with a release of your game named "140430-1345", $from_version is equal to 1404301345
     */
    public function upgradeTableDb($from_version)
    {
        if ($from_version <= 2410231142) {
            $newSchema = self::DbQuery('SHOW COLUMNS FROM `player` LIKE \'player_faction_side\'')->num_rows === 1;
            if (!$newSchema) {
                $sql = "ALTER TABLE DBPREFIX_player ADD `player_faction_side` TINYINT DEFAULT 2;";
                self::applyDbUpgradeToAllDB($sql);
            }
        }
        if ($from_version <= 2503211724) {
            $this->updateDBTableCustom();
        }
    }

    function updateDBTableCustom()
    {
    }

    /////////////////////////////////////////////////////////////
    // Exposing protected methods, please use at your own risk //
    /////////////////////////////////////////////////////////////

    // Exposing protected method getCurrentPlayerId
    public static function getCurrentPId()
    {
        return self::get()->getCurrentPlayerId();
    }

    // Exposing protected method translation
    public static function translate($text)
    {
        return self::_($text);
    }

    public static function a()
    {
//        var_dump(Stack::get());
    }
}
