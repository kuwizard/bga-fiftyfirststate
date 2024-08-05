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
use STATE\Core\Stack;
use STATE\Core\Stats;
use STATE\Managers\Connections;
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
        Globals::setupNewGame();
        $this->giveEachPlayerCardsSetup();
        $this->activeNextPlayer();
    }

    /**
     * @param Player[] $players
     * @param int $amount
     * @return void
     */
    private function giveEachPlayerCardsSetup()
    {
        /** @var Player $player */
        foreach (Players::getAll() as $player) {
            $player->drawCards(GLOBAL_START_CARDS);
        }
    }

    /*
     * getAllDatas:
     */
    public function getAllDatas()
    {
        $currentPlayerId = Players::getCurrentId();
        return [
            'players' => Players::getUiData($currentPlayerId),
            'firstPlayerId' => Globals::getFirstPlayerId(),
        ];
    }

    /*
     * getGameProgression:
     */
    function getGameProgression()
    {
        return 50;
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
    public function zombieTurn($state, $activePlayer)
    {

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
