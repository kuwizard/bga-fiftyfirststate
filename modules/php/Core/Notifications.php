<?php
namespace STATE\Core;

use STATE\Models\Location;
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
     * @return void
     */
    public static function handChanged($player)
    {
        $resources = ['player' => $player, 'hand' => $player->getHand()];
        self::notify($player, 'handChanged', '', $resources);
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function resourcesSpentFaction($player, $resources, $order)
    {
        self::notifyAll('resourcesSpentFaction', '', [
            'resources' => $resources,
            'order' => $order,
            'player' => $player,
        ]);
    }

    /**
     * @param Player $player
     * @param Location $location
     * @param string $factionRow
     * @return void
     */
    public static function locationBuilt($player, $location, $factionRow)
    {
        self::notifyAll('locationBuilt', '', [
            'player' => $player,
            'location' => $location,
            'factionRow' => $factionRow,
        ]);
    }

    /**
     * @param Player $player
     * @param int $id
     * @param int $resource
     * @return void
     */
    public static function locationDealMade($player, $id, $resource)
    {
        self::notifyAll('locationDealMade', '', [
            'player' => $player,
            'id' => $id,
            'resource' => $resource,
        ]);
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
