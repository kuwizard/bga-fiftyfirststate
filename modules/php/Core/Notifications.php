<?php
namespace STATE\Core;

use STATE\Models\Player;

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

    /**
     * @param Player $player
     * @param array $resources
     * @return void
     */
    public static function resourcesChanged($player, $resources)
    {
        $resources = ['player' => $player, 'resources' => $resources];
        self::notifyAll('resourcesChanged', '', $resources);
    }

    /**
     * @param Player $player
     * @param int[] $resources
     * @return void
     */
    public static function locationsDiscarded($player, $locationsIds)
    {
        $resources = ['player' => $player, 'locationsIds' => $locationsIds];
        self::notify($player, 'locationsDiscarded', '', $resources);
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
