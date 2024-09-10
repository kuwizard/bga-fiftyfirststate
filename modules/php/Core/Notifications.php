<?php
namespace STATE\Core;

use STATE\Models\Location;
use STATE\Models\Player;

class Notifications
{
    /*************************
     **** GENERIC METHODS ****
     *************************/
    private static function notifyAll($name, $msg, $data = [])
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
     * @param array $resources
     * @param string $resourceName
     * @return void
     */
    public static function resourcesLocationChanged($player, $locationId, $resourceName)
    {
        self::notifyAll(
            'resourcesLocationChanged',
            '',
            ['player' => $player, 'locationId' => $locationId, 'resourceName' => $resourceName]
        );
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function handChanged($player)
    {
        $resources = ['player' => $player, 'hand' => $player->getHand()->toArray()];
        self::notify($player, 'handChanged', '', $resources);
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function resourcesSpentFaction($player, $resources, $order)
    {
        self::notifyAll('resourcesSpentFaction', '', [
            'player' => $player,
            'order' => $order,
            'resources' => $resources,
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

    /**
     * @param Player $player
     * @param int $id
     * @param int $newDiscardCount
     * @return void
     */
    public static function locationDiscarded($player, $id, $newDiscardCount)
    {
        self::notifyAll('locationDiscarded', '', [
            'player' => $player,
            'id' => $id,
            'newDiscardCount' => $newDiscardCount,
        ]);
    }

    /**
     * @param Player $player
     * @param int $id
     * @param array $resources
     * @return void
     */
    public static function resourcesPlacedOnLocation($player, $id, $resources)
    {
        self::notifyAll('resourcesPlacedOnLocation', '', [
            'player' => $player,
            'id' => $id,
            'resources' => $resources,
        ]);
    }

    public static function locationRuined(Player $player, int $id)
    {
        self::notifyAll('locationRuined', '', [
            'player' => $player,
            'id' => $id,
        ]);
    }

    public static function connectionActivated($player, $id)
    {
        self::notifyAll('connectionActivated', '', [
            'player' => $player,
            'id' => $id,
        ]);
    }

    public static function newConnections(array $connections)
    {
        self::notifyAll('newConnections', '', [
            'connections' => $connections,
        ]);
    }

    public static function playerPassed($player)
    {
        self::notifyAll('playerPassed', '', [
            'player' => $player,
        ]);
    }

    public static function playerGotResourcesFromStorage(Player $player, int $locationId, array $resources)
    {
        self::notifyAll('playerGotResourcesFromStorage', '', [
            'player' => $player,
            'locationId' => $locationId,
            'resources' => $resources,
        ]);
    }

    public static function playersResetAllResources()
    {
        self::notifyAll('playersResetAllResources', '');
    }

    public static function lastRound($player)
    {
        self::notifyAll('lastRound', clienttranslate('${player_name} reached 25 ${scoreIcon}! End of game is triggered!'), [
            'player' => $player,
            'scoreIcon' => '',
        ]);
    }

    public static function dealDiscarded(Player $player, Location $discarded, int $newDiscardCount, string $resource)
    {
        self::notifyAll('dealDiscarded', '', [
            'player' => $player,
            'discarded' => $discarded,
            'newDiscardCount' => $newDiscardCount,
            'resourceRemoved' => $resource,
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
