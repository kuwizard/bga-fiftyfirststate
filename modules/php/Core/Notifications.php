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
            'newPosition' => $player->getPositionOfLocationInHand($location),
            'source' => $source,
            'location' => $location,
            'i18n' => ['locationName'],
        ];
        self::notifyAll('locationPicked', clienttranslate('${player_name} picks a Location ${locationName}'), $resources);
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

    public static function locationBuilt(Player $player, Location $location, Location $oldLocation = null, int $resource = null)
    {
        $msg = $oldLocation ? clienttranslate(
            '${player_name} spends ${resourcesList} to discard ${locationName2} and deploy ${locationName} in the ${factionRowName} row'
        )
            : clienttranslate('${player_name} spends ${spendList} to build ${locationName} in the ${factionRowName} row');
        self::notifyAll('locationBuilt', $msg, [
            'player' => $player,
            'location' => $location,
            'location2' => $oldLocation,
            'factionRow' => $location->getFactionRow(),
            'factionRowName' => $location->getFactionRowName(),
            'spendList' => ResourcesHelper::getResourceNames(array_fill(0, $location->getDistance(), RESOURCE_ARROW_GREY)),
            'resourcesList' => is_null($resource) ? null : [ResourcesHelper::getResourceName($resource)],
            'i18n' => ['locationName', 'locationName2', 'factionRowName'],
            'preserve' => ['location2'],
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
        $msg = clienttranslate(
            '${player_name} makes a deal with ${locationName}, spending ${spendList}, and gets ${resourcesList}'
        );
        $resource = ResourcesHelper::getResourceName($location->getDeals()[0]);
        self::notifyAll('locationDealMade', $msg, [
            'player' => $player,
            'location' => $location,
            'spendList' => ResourcesHelper::getResourceNames(array_fill(0, $location->getDistance(), RESOURCE_ARROW_BLUE)),
            'resource' => $resource,
            'resourcesList' => [$resource],
            'i18n' => ['locationName'],
        ]);
    }

    public static function locationDiscarded(Player $player, Location $location, bool $discardResources = true)
    {
        self::notifyAll('locationDiscarded', '', [
            'player' => $player,
            'location' => $location,
            'newDiscardCount' => Locations::countInLocation(LOCATION_DISCARD),
            'discardResources' => $discardResources,
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
        $msg = clienttranslate('${player_name} razes ${locationName}, spending ${spendList}, and gets ${resourcesList}');
        self::notifyAll('locationDiscarded', $msg, [
            'player' => $player,
            'location' => $location,
            'spendList' => ResourcesHelper::getResourceNames(array_fill(0, $location->getDistance(), RESOURCE_ARROW_RED)),
            'newDiscardCount' => Locations::countInLocation(LOCATION_DISCARD),
            'resourcesList' => ResourcesHelper::getResourceNames($location->getSpoils()),
            'i18n' => ['locationName'],
        ]);
    }

    public static function resourcesPlacedOnLocation(Player $player, int $id, array $resources, $location = null)
    {
        if ($location) {
            $msg = clienttranslate('${player_name} places ${resourcesList} to the ${locationName} as a building bonus');
        } else {
            $msg = '';
        }
        self::notifyAll('resourcesPlacedOnLocation', $msg, [
            'player' => $player,
            'id' => $id,
            'resources' => $resources,
            'resourcesList' => $resources,
            'location' => $location,
            'locationName' => $location ? $location->getName() : null,
            'i18n' => ['locationName'],
        ]);
    }

    public static function locationRuined(Player $owner, Location $location, Player $attacker)
    {
        $msg = clienttranslate(
            '${player_name} razes other player\'s ${locationName} getting ${resourcesList}. ${victim_name} gets ${resourcesList2} as a bonus'
        );
        self::notifyAll('locationRuined', $msg, [
            'player' => $attacker,
            'location' => $location,
            'victim' => $owner,
            'resourcesList' => ResourcesHelper::getResourceNames($location->getSpoils()),
            'resourcesList2' => ResourcesHelper::getResourceNames($location->getDeals()),
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
        self::notifyAll(
            'newConnections',
            clienttranslate('New round starts with a phase 1: Lookout. Players should select new Locations'),
            [
                'connections' => $connections,
            ]
        );
    }

    public static function playerPassed($player)
    {
        self::notifyAll('playerPassed', '', [
            'player' => $player,
        ]);
    }

    public static function playerGotResourcesFromStorage(Player $player, Location $location, array $resources)
    {
        $msg = clienttranslate('${player_name} gets all resources back from ${locationName}');
        self::notifyAll('playerGotResourcesFromStorage', $msg, [
            'player' => $player,
            'location' => $location,
            'resources' => $resources,
            'i18n' => ['locationName'],
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

    public static function locationsReshuffle()
    {
        self::notifyAll('locationsReshuffle', clienttranslate('Locations deck has been reshuffled'), [
            'deckCount' => Locations::countInLocation(LOCATION_DECK),
            'discardCount' => Locations::countInLocation(LOCATION_DISCARD),
        ]);
    }

    public static function connectionsReshuffle()
    {
        self::message(clienttranslate('Connections decks have been reshuffled'));
    }

    public static function message(string $message, array $data = [])
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

    public static function resourceStolen(Player $activePlayer, Player $victim, int $resource)
    {
        $msg = clienttranslate('${player_name} steals ${resourcesList} from ${victim_name}');
        self::message($msg, [
            'player' => $activePlayer,
            'victim' => $victim,
            'resourcesList' => ResourcesHelper::getResourceNames([$resource]),
        ]);
    }

    public static function actionUsed(Player $player, int $activatorId, array $spend, array $bonus, Player|null $victim): void
    {
        if ($activatorId === 0) {
            debug_print_backtrace();
            throw new \BgaVisibleSystemException('$activatorId is 0');
        }
        $location = Locations::get($activatorId);
        $from = $activatorId >= FACTION_NEW_YORK && $activatorId <= FACTION_MERCHANTS + 3
            ? clienttranslate('a faction action')
            : $location->getName();
        $spendList = [];
        $deal = null;
        foreach ($spend as $item) {
            if (is_integer($item)) {
                $spendList[] = $item;
            } else {
                // It should be a Location containing a deal!
                $deal = $item;
            }
        }
        if ($victim) {
            $msg = clienttranslate(
                '${player_name} uses ${from} as an Open Production, spends ${spendList} to gain ${resourcesList}. ${victim_name} gains ${spendList} as the owner'
            );
        } else if ($deal) {
            if (empty($spendList)) {
                $msg = clienttranslate(
                    '${player_name} uses ${from}, spends a deal giving ${dealResource} to gain ${resourcesList}'
                );
            } else {
                $msg = clienttranslate(
                    '${player_name} uses ${from}, spends ${spendList} and a deal giving ${dealResource} to gain ${resourcesList}'
                );
            }
        } else {
            $msg = clienttranslate(
                '${player_name} uses ${from}, spends ${spendList} to gain ${resourcesList}'
            );
        }

        self::message($msg, [
            'player' => $player,
            'from' => $from,
            'spendList' => ResourcesHelper::getResourceNames($spendList),
            'resourcesList' => ResourcesHelper::getResourceNames($bonus),
            'victim' => $victim,
            'dealResource' => $deal ? ResourcesHelper::getResourceNames($deal->getDeals()) : null,
            'i18n' => ['from'],
        ]);
    }

    public static function endOfGameVPGained(Player $player, int $amount, int $total)
    {
        $msg = clienttranslate(
            '${player_name} gets ${amount}${resourcesList} for each Location in their State increasing total to ${total}'
        );
        self::notifyAll('endOfGameVPGained', $msg, [
            'player' => $player,
            'amount' => $amount,
            'total' => $total,
            'resourcesList' => ResourcesHelper::getResourceNames([RESOURCE_VP]),
        ]);
    }

    public static function playerPhaseTwoProductionFaction($player, $factionProd)
    {
        $msg = clienttranslate('${player_name} gets ${resourcesList} from Faction production');
        self::notifyAll('playerPhaseTwoProduction', $msg, [
            'player' => $player,
            'resourcesList' => ResourcesHelper::getResourceNames($factionProd),
        ]);
    }

    public static function playerPhaseTwoProductionLocations($player, $prodLocations)
    {
        $msg = clienttranslate('Also ${player_name} gets ${resourcesList} from Production Locations');
        self::notifyAll('playerPhaseTwoProduction', $msg, [
            'player' => $player,
            'resourcesList' => ResourcesHelper::getResourceNames($prodLocations),
        ]);
    }

    public static function playerPhaseTwoDeals($player, $dealsProd)
    {
        $msg = clienttranslate('Finally, ${player_name} gets ${resourcesList} from deals');
        self::notifyAll('playerPhaseTwoProduction', $msg, [
            'player' => $player,
            'resourcesList' => ResourcesHelper::getResourceNames($dealsProd),
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

        if (isset($data['victim'])) {
            $data['victim_id'] = $data['victim']->getId();
            $data['victim_name'] = $data['victim']->getName();
            unset($data['victim']);
        }
    }
}

?>
