<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * fiftyfirststate.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

use STATE\Core\Globals;
use STATE\Core\Preferences;
use STATE\Core\Stack;
use STATE\Core\Stats;
use STATE\Managers\Connections;
use STATE\Managers\Factions;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Models\Player;

require_once APP_GAMEMODULE_PATH . 'module/table/table.game.php';

$swdNamespaceAutoload = function ($class) {
    $classParts = explode('\\', $class);
    if ($classParts[0] === 'STATE') {
        array_shift($classParts);
        $file = dirname(__FILE__) . '/modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            var_dump('Cannot find file : ' . $file);
        }
    }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

class Fiftyfirststate extends Table
{
    use STATE\States\RoundTrait;
    use STATE\States\DiscardCardsGameStartTrait;
    use STATE\States\PhaseOneLookoutTrait;
    use STATE\States\PhaseTwoProductionTrait;
    use STATE\States\PhaseThreeActionTrait;
    use STATE\States\PhaseFourCleanupTrait;
    use STATE\States\ChooseResourceSourceTrait;
    use STATE\States\ChooseResourceToStoreTrait;
    use STATE\States\DevelopTrait;
    use STATE\States\ChoosePlayerToStealTrait;
    use STATE\States\ActivateSecondTimeTrait;
    use STATE\States\SpecificLocationsActionsTrait;
    use STATE\States\ConfirmTurnEndTrait;
    use STATE\States\ChooseFactionTrait;

    public static $instance = null;

    function __construct()
    {
        parent::__construct();
        self::$instance = $this;
        self::initGameStateLabels([]);
    }

    public static function get()
    {
        return self::$instance;
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return 'fiftyfirststate';
    }

    /*
     * setupNewGame:
     */
    protected function setupNewGame($players, $options = [])
    {
        Stats::setupNewGame();
        Players::setupNewGame($players, $options);
        Locations::setupNewGame();
        Connections::setupNewGame();
        Preferences::setupNewGame($players, $this->player_preferences);
        Globals::setupNewGame();
    }

    /*
     * getAllDatas:
     */
    public function getAllDatas()
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
        if ($from_version <= 2412211311) {
            $this->updateDBTableCustom();
        }
    }

    function updateDBTableCustom()
    {
        $newSchema = self::DbQuery('SHOW COLUMNS FROM `locations` LIKE \'is_defended\'')->num_rows === 1;
        if (!$newSchema) {
            $sql = "ALTER TABLE DBPREFIX_locations ADD `is_defended` tinyint NOT NULL DEFAULT 0;";
            self::applyDbUpgradeToAllDB($sql);
        }
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
