<?php
namespace STATE\Core;

use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;
use STATE\Models\Connection;
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
     * @return void
     */
    public static function handChanged(Player $player)
    {
        $resources = [
            'player' => $player,
            'hand' => $player->getHand()->toArray(),
        ];
        self::notify($player, 'handChanged', '', $resources);
    }

    public static function deckChanged()
    {
        self::notifyAll('deckChanged', '', ['deckCount' => Locations::countInLocation(LOCATION_DECK)]);
    }

    /**
     * @return void
     */
    public static function locationPicked(Player $player, Location $location = null, string $source = '')
    {
        $resources = [
            'player' => $player,
            'hand' => $player->getHand()->toArray(),
            'source' => $source,
            'location' => $location,
            'i18n' => ['locationName'],
        ];
        self::notifyAll('locationPicked', clienttranslate('${player_name} picks a location ${locationName}'), $resources);
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

    public static function locationBuilt(Player $player, Location $location, Location $deployed = null, int $resource = null)
    {
        $msg = $deployed ? clienttranslate(
            '${player_name} spends ${resourcesList} to discard ${locationName2} and deploy ${locationName} in the ${factionRowName} row'
        )
            : clienttranslate('${player_name} builds ${locationName} in the ${factionRowName} row');
        self::notifyAll('locationBuilt', $msg, [
            'player' => $player,
            'location' => $location,
            'factionRow' => $location->getFactionRow(),
            'factionRowName' => $location->getFactionRowName(),
            'resourcesList' => is_null($resource) ? null : [ResourcesHelper::getResourceName($resource)],
            'i18n' => ['locationName', 'locationName2', 'factionRowName'],
        ]);
    }

    /**
     * @param Player $player
     * @param int $id
     * @param int $resource
     * @return void
     */
    public static function locationDealMade(Player $player, Location $location)
    {
        $msg = clienttranslate('${player_name} makes a deal with ${locationName} and gets ${resourcesList}');
        $resource = ResourcesHelper::getResourceName($location->getDeals()[0]);
        self::notifyAll('locationDealMade', $msg, [
            'player' => $player,
            'location' => $location,
            'resource' => $resource,
            'resourcesList' => [$resource],
            'i18n' => ['locationName'],
        ]);
    }

    /**
     * @param Player $player
     * @param Location $location
     * @param int $newDiscardCount
     * @return void
     */
    public static function locationDiscarded($player, $location)
    {
        self::notifyAll('locationDiscarded', '', [
            'player' => $player,
            'location' => $location,
            'newDiscardCount' => Locations::countInLocation(LOCATION_DISCARD),
        ]);
    }

    /**
     * @param Player $player
     * @param Location $location
     * @param int $newDiscardCount
     * @return void
     */
    public static function locationRazed($player, $location)
    {
        $msg = clienttranslate('${player_name} razes ${locationName} and gets ${resourcesList}');
        self::notifyAll('locationDiscarded', $msg, [
            'player' => $player,
            'location' => $location,
            'newDiscardCount' => Locations::countInLocation(LOCATION_DISCARD),
            'resourcesList' => ResourcesHelper::getResourceNames($location->getSpoils()),
            'i18n' => ['locationName'],
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

    public static function connectionTaken(Player $player, int $id, string $deckName)
    {
        $msg = clienttranslate('${player_name} spends ${spendList} to take a Connection card from a ${deckName} deck');
        self::notifyAll('connectionTaken', $msg, [
            'player' => $player,
            'id' => $id,
            'deckName' => $deckName,
            'spendList' => ResourcesHelper::getResourceNames([RESOURCE_WORKER, RESOURCE_WORKER]),
            'i18n' => ['deckName'],
        ]);
    }

    public static function connectionPlayed(Player $player, int $id, Connection $connection)
    {
        if (empty($connection->getSpendRequirements())) {
            $msg = clienttranslate('${player_name} plays a Connection card from hand and gets ${resourcesList}');
        } else {
            $msg = clienttranslate(
                '${player_name} plays a Connection card from hand, spends ${spendList} and gets ${resourcesList}'
            );
        }
        self::notifyAll('connectionPlayed', $msg, [
            'player' => $player,
            'id' => $id,
            'spendList' => ResourcesHelper::getResourceNames($connection->getSpendRequirements()),
            'resourcesList' => $connection->getBonusUi(),
        ]);
    }

    public static function newConnections(array $connections)
    {
        self::notifyAll('newConnections', clienttranslate('New round starts. Top cards of each Connections pile are revealed'), [
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

    public static function reshuffle()
    {
        self::notifyAll('reshuffle', clienttranslate('The deck have been reshuffled'), [
            'deckCount' => Locations::countInLocation(LOCATION_DECK),
            'discardCount' => Locations::countInLocation(LOCATION_DISCARD),
        ]);
    }

    public static function message(string $message, array $data)
    {
        self::notifyAll('message', $message, $data);
    }

    public static function discardTwoCards(Player $player)
    {
        self::message(clienttranslate('${player_name} discards two locations'), [
            'player' => $player,
        ]);
    }

    public static function gotProductionAndOrBuildingBonuses(
        Player $player,
        Location $location,
        array $product,
        array $buildingBonus)
    {
        if (!empty($product)) {
            $msg = clienttranslate('${locationName} is a Production so ${player_name} gets ${resourcesList}');
            $resourcesList = $product;
        }
        if (!empty($buildingBonus)) {
            $msg = clienttranslate('${locationName} has a building bonus so ${player_name} gets ${resourcesList}');
            $resourcesList = $buildingBonus;
        }
        if (!empty($product) && !empty($buildingBonus)) {
            $msg = clienttranslate(
                '${locationName} is a Production with a building bonus so ${player_name} gets ${resourcesList}'
            );
            $resourcesList = array_merge($product, $buildingBonus);
        }
        self::message($msg, [
            'player' => $player,
            'location' => $location,
            'resourcesList' => ResourcesHelper::getResourceNames($resourcesList),
            'i18n' => ['locationName'],
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
