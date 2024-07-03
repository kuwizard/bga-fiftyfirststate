<?php
namespace STATE\Core;

use STATE\Managers\Languages;

class Notifications
{
    /*************************
     **** GENERIC METHODS ****
     *************************/
    private static function notifyAll($name, $msg, $data)
    {
        self::updateArgs($data);
        Game::get()->notifyAllPlayers($name, $msg, $data);
    }

    private static function notify($player, $name, $msg, $data)
    {
        $pId = is_int($player) ? $player : $player->getId();
        self::updateArgs($data);
        Game::get()->notifyPlayer($pId, $name, $msg, $data);
    }

    /*********************
     **** UPDATE ARGS ****
     *********************/
    /*
     * Automatically adds some standard field about player and/or card
     */
    protected static function updateArgs(&$data)
    {
        if (isset($data['player'])) {
            $data['player_id'] = $data['player']->getId();
            $data['player_name'] = $data['player']->getName();
            unset($data['player']);
        }
    }
}

?>
