<?php

namespace STATE\Models;

use JsonSerializable;
use STATE\Core\Globals;
use STATE\Core\Notifications;
use STATE\Core\Preferences;
use STATE\Helpers\Collection;
use STATE\Helpers\DB_Manager;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Connections;
use STATE\Managers\Factions;
use STATE\Managers\Locations;
use STATE\Managers\Players;
use STATE\Managers\Resources;

/*
 * Player: all utility functions concerning a player
 */

class Player extends DB_Manager implements JsonSerializable
{
    protected static $table = 'player';
    protected static $primary = 'player_id';

    /**
     * @var int
     */
    protected $id;
    /**
     * @var int
     */
    protected $no; // natural order
    /**
     * @var string
     */
    protected $name; // player name
    /**
     * @var string
     */
    protected $color;
    /**
     * @var int
     */
    protected $faction;
    /**
     * @var int
     */
    protected $score = 0;
    /**
     * @var boolean
     */
    protected $zombie = false;
    /**
     * @var int
     */
    protected $gun;
    /**
     * @var int
     */
    protected $iron;
    /**
     * @var int
     */
    protected $brick;
    /**
     * @var int
     */
    protected $worker;
    /**
     * @var int
     */
    protected $arrowGrey;
    /**
     * @var int
     */
    protected $arrowRed;
    /**
     * @var int
     */
    protected $arrowBlue;
    /**
     * @var int
     */
    protected $arrowUni;
    /**
     * @var int
     */
    protected $ammo;
    /**
     * @var int
     */
    protected $defence;
    /**
     * @var int
     */
    protected $devel;
    /**
     * @var boolean
     */
    protected $passed;
    /**
     * @var array
     */
    private $recentlyDrawnLocations;

    public function __construct($row)
    {
        if ($row != null) {
            $this->id = (int) $row['player_id'];
            $this->no = (int) $row['player_no'];
            $this->name = $row['player_name'];
            $this->color = $row['player_color'];
            $this->faction = $this->getFaction();
            $this->score = (int) $row['player_score'];
            $this->zombie = (int) $row['player_zombie'] === 1;
            $this->fuel = (int) $row['player_fuel'];
            $this->gun = (int) $row['player_gun'];
            $this->iron = (int) $row['player_iron'];
            $this->brick = (int) $row['player_brick'];
            $this->worker = (int) $row['player_worker'];
            $this->arrowGrey = (int) $row['player_arrow_grey'];
            $this->arrowRed = (int) $row['player_arrow_red'];
            $this->arrowBlue = (int) $row['player_arrow_blue'];
            $this->arrowUni = (int) $row['player_arrow_uni'];
            $this->ammo = (int) $row['player_ammo'];
            $this->defence = (int) $row['player_defence'];
            $this->devel = (int) $row['player_devel'];
            $this->passed = (int) $row['player_passed'] === 1;
        }
        $this->recentlyDrawnLocations = [];
    }

    /*
     * Getters
     */
    public function getId()
    {
        return $this->id;
    }

    public function getNo()
    {
        return $this->no;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function isZombie()
    {
        return $this->zombie;
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }

    public function getPref($prefId)
    {
        return Preferences::get($this->id, $prefId);
    }

    public function getRecentlyDrawnLocations(): array
    {
        return $this->recentlyDrawnLocations;
    }

    /**
     * @param string $color
     * @return int
     */
    public function getFaction()
    {
        return [
            "bcc6cc" => FACTION_NEW_YORK,
            "ffa500" => FACTION_APPALACHIAN,
            "ff0000" => FACTION_MUTANTS,
            "0000ff" => FACTION_MERCHANTS,
        ][$this->color];
    }

    /**
     * @param string $color
     * @return int
     */
    public function getFactionUI()
    {
        return ($this->getFaction() / 10) - 50;
    }

    /**
     * @return int[]
     */
    public function getDeals()
    {
        return $this->combineResources(LOCATION_DEALS);
    }

    /**
     * @return int[]
     */
    public function getProduction()
    {
        return $this->combineResources(LOCATION_BOARD);
    }

    /**
     * @return int[]
     */
    public function getFactionProduction()
    {
        $name = 'STATE\Data\Factions\\' . Factions::getName($this->getFaction());
        /** @var Faction $faction */
        $faction = new $name;
        return $faction->getProduction();
    }

    /**
     * @return Act[]
     */
    public function getFactionActions()
    {
        $name = 'STATE\Data\Factions\\' . Factions::getName($this->getFaction());
        /** @var Faction $faction */
        $faction = new $name;
        return $faction->getActions();
    }

    /**
     * @return array
     */
    private function getUsedFactionActions()
    {
        if ($this->isPassed()) {
            return [];
        }
        $actions = $this->getFactionActions();
        $dbActions = Factions::getAllForFaction($this->faction);
        $used = [];
        foreach ($actions as $id => $action) {
            $key = array_search(strval($id), array_column($dbActions, 'action_number'));
            if ((int) $dbActions[$key]['used'] === 1) {
                $used[$id] = $action->getSpendRequirementsUIRemoveCard();
            }
        }
        return $used;
    }

    /**
     * @param Act[] $actions
     * @return array
     */
    public function getAvailableFactionActions($actions = null)
    {
        if (!$actions) {
            $actions = $this->getFactionActions();
        }
        $availableActions = [];
        $dbActions = Factions::getAllForFaction($this->faction);
        foreach ($actions as $id => $action) {
            $isAvailable = true;
            $requirements = array_count_values($action->getSpendRequirements());
            foreach ($requirements as $requirement => $amount) {
                if ($this->getResource($requirement, false, true) < $amount) {
                    $isAvailable = false;
                    break;
                }
            }
            if ($isAvailable) {
                $key = array_search(strval($id), array_column($dbActions, 'action_number'));
                if ((int) $dbActions[$key]['used'] === 0) {
                    $availableActions[$id] = $action;
                }
            }
        }
        return $availableActions;
    }

    /**
     * @return int
     */
    public function getHandAmount()
    {
        return Locations::countInLocation([LOCATION_HAND, $this->id]);
    }

    /**
     * @return Collection
     */
    public function getHand()
    {
        return Locations::getHand($this->id);
    }

    /**
     * @param int[] $array_values
     * @return array
     */
    public function getResourcesWithNames($resources)
    {
        $result = [];
        foreach ($resources as $resource) {
            $result[ResourcesHelper::getResourceName($resource)] = $this->getResource($resource);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getResource(int $resource, bool $factionOnly = true, bool $considerJoker = false)
    {
        $resourceName = ResourcesHelper::getResourceName($resource);
        if ($resource === RESOURCE_CARD) {
            return $this->getHandAmount();
        } else if ($resource === RESOURCE_DEAL) {
            return 0;
        } else if ($resource === RESOURCE_ANY_OF_MAIN) {
            $arrayOfFalses = array_fill(0, count(MAIN_RESOURCES_LIST), false);
            // for each resource it calls $this->getResource(resource, false)
            return array_sum(array_map([$this, 'getResource'], MAIN_RESOURCES_LIST, $arrayOfFalses));
        } else {
            $playerResource = $this->$resourceName;
            if ($factionOnly) {
                $cardResource = 0;
            } else {
                $allCardsResources = array_count_values(Resources::getMultiple(Locations::getBoard($this->id)->getIds()));
                $cardResource = $allCardsResources[$resource] ?? 0;
            }
            if ($considerJoker) {
                $joker = Resources::getJokerFor($resource);
                $jokersAmount = $joker ? $this->getResource($joker) : 0;
            } else {
                $jokersAmount = 0;
            }
            return $playerResource + $cardResource + $jokersAmount;
        }
    }

    /**
     * @return array
     */
    public function getPlayableLocationsWithCardWarnings()
    {
        $hand = $this->getHand()->toArray();
        $result = [];
        foreach ($hand as $location) {
            $availableActions = $this->getAvailableLocationActions($location);
            if (!empty($availableActions)) {
                $result[$location->getId()] = false; // false is showing we don't need a card warning
            }
        }
        foreach ($this->getBoard() as $location) {
            if ($location instanceof Action) {
                $isActivatable = $location->isActivatable();
                if ($isActivatable) {
                    foreach (array_count_values($location->getSpendRequirements()) as $requirement => $amount) {
                        if ($requirement === RESOURCE_DEAL) {
                            $isActivatable = !empty($this->getDeals());
                        } else if ($this->getResource($requirement, false, true) < $amount) {
                            $isActivatable = false;
                            break;
                        }
                    }
                }
                if ($isActivatable) {
                    $result[$location->getId()] = in_array(RESOURCE_CARD, $location->getBonus());
                }
            }
        }
        return $result;
    }

    /**
     * @return int[]
     */
    public function getPlayableConnectionsIds()
    {
        $connections = Connections::getInLocation([LOCATION_HAND, $this->id]);
        return $connections->filter(function (Connection $connection) {
            $requirements = $connection->getSpendRequirements();
            foreach (array_count_values($requirements) as $requirement => $amount) {
                if ($this->getResource($requirement, false, true) < $amount) {
                    return false;
                }
            }
            return true;
        })->getIds();
    }

    /**
     * @param Location $location
     * @return string[]
     */
    public function getAvailableLocationActions($location)
    {
        $availableActions = [];
        /** @var Location $location */
        if ($this->getResource(RESOURCE_ARROW_RED, false, true) >= $location->getDistance()) {
            $availableActions['raze'] = in_array(RESOURCE_CARD, $location->getSpoils());
        }
        if ($this->getResource(RESOURCE_ARROW_GREY, false, true) >= $location->getDistance()) {
            $availableActions['build'] = in_array(RESOURCE_CARD, $location->getBuildingBonus($this));
        }
        if ($this->getResource(RESOURCE_ARROW_BLUE, false, true) >= $location->getDistance()) {
            $availableActions['deal'] = in_array(RESOURCE_CARD, $location->getDeals());
        }
        return $availableActions;
    }

    /**
     * @param int $icon
     * @return int[]
     */
    public function getBoardIcons(int $icon)
    {
        $allIcons = array_merge(
            ...$this->getBoard()->map(function ($location) {
            /** @var Location $location */
            return $location->getIcons();
        })->toArray()
        );
        return array_intersect($allIcons, [$icon]);
    }

    /**
     * @return Collection
     */
    public function getBoard($includeRuins = false)
    {
        $withRuins = Locations::getBoard($this->id);
        if ($includeRuins) {
            return $withRuins;
        } else {
            return Locations::getBoard($this->id)->filter(function (Location $location) {
                return !$location->isRuined();
            });
        }
    }

    /**
     * @return int
     */
    public function getTotalResourcesCount()
    {
        return array_sum(array_map(function ($resource) {
            return $this->getResource($resource);
        }, ALL_RESOURCES_LIST));
    }

    /**
     * @return int[]
     */
    public function getResourcesNotZero($requested)
    {
        return array_filter($requested, function ($resource) {
            return $this->getResource($resource) > 0;
        });
    }

    public function getPositionOfLocationInHand($location)
    {
        $locatedLocation = $this->getHand()->filter(function ($loc) use ($location) {
            return $loc->getId() === $location->getId();
        });
        return array_keys($locatedLocation->toAssoc())[0];
    }

    /**
     * @param array $resources
     * @return void
     */
    public function increaseResources($resources)
    {
        foreach ($resources as $type => $amount) {
            $this->increaseResource($type, $amount);
        }
    }

    public function increaseResource(int $type, int $amount = 1): int
    {
        $name = ResourcesHelper::getResourceName($type);
        if ($type === RESOURCE_CARD) {
            $newLocations = $this->drawCards($amount);
            foreach ($newLocations->toArray() as $location) {
                $this->recentlyDrawnLocations[$this->getPositionOfLocationInHand($location)] = $location;
            }
            return 0; // We don't need it actually
        } else {
            $newAmount = $this->{$name} + $amount;
            $this->{$name} = $newAmount;
            $this->updateResource(ResourcesHelper::getDBName($type), $newAmount);
            if ($type === RESOURCE_VP && $newAmount >= GLOBAL_END_OF_GAME_VP) {
                if (!Globals::isLastRound()) {
                    Globals::setLastRound(true);
                    Notifications::lastRound($this);
                }
            }
        }
        return $newAmount;
    }

    /**
     * @param int $type
     * @param int $amount
     * @return void
     */
    public function decreaseResource($type, $amount = 1)
    {
        $name = ResourcesHelper::getResourceName($type);
        $resourceAmount = $this->{$name};
        $newAmount = $resourceAmount - $amount;
        if ($newAmount < 0) {
            throw new \BgaVisibleSystemException(
                "Something's wrong. You try to decrease resource {$name} to a negative value. You have {$resourceAmount}, amount to lose - {$amount}"
            );
        }
        $this->{$name} = $newAmount;
        $this->updateResource(ResourcesHelper::getDBName($type), $newAmount);
    }

    /**
     * @param string $name
     * @param int $amount
     * @return void
     */
    private function updateResource($name, $amount)
    {
        self::DB()
            ->update([$name => $amount])
            ->wherePlayer($this->id)
            ->run();
    }

    public function discardSingle(int $cardId): void
    {
        $this->discard([$cardId]);
    }

    public function discard(array $cardIds): void
    {
        Locations::discard($cardIds);
    }

    public function drawCards($amount = 1)
    {
        return Locations::draw($this, $amount);
    }

    private function combineResources($location)
    {
        $locationsCards = Locations::getInLocation([$location, $this->id]);
        if ($location === LOCATION_BOARD) {
            $locationsCards = $locationsCards->filter(function ($locationCard) {
                /** @var Location $locationCard */
                return $locationCard instanceof Production;
            });
        }
        $combined = [];
        /** @var Location | Production $locationCard */
        foreach ($locationsCards->toArray() as $locationCard) {
            if ($location === LOCATION_DEALS) {
                $resources = $locationCard->getDeals();
            } else if ($location === LOCATION_BOARD) {
                $resources = $locationCard->getProduct($this);
            } else {
                throw new \BgaVisibleSystemException(
                    'Trying to get resources from unknown location (' . $location . ')'
                );
            }
            $combined = array_merge($combined, $resources);
        }
        return $combined;
    }

    public function markAsPassed()
    {
        Players::markAsPassed($this->id);
    }

    public function setTieBreaker(int $value)
    {
        Players::setScoreAux($this->id, $value);
    }

    public function jsonSerialize($currentPlayerId = null)
    {
        $current = $this->id === $currentPlayerId;
        $data = [
            'id' => $this->id,
            'no' => $this->no,
            'name' => $this->name,
            'color' => $this->color,
            'score' => $this->score,
            'faction' => $this->getFactionUI(),
            'resources' => [
                'fuel' => $this->fuel,
                'gun' => $this->gun,
                'iron' => $this->iron,
                'brick' => $this->brick,
                'worker' => $this->worker,
                'arrowGrey' => $this->arrowGrey,
                'arrowRed' => $this->arrowRed,
                'arrowBlue' => $this->arrowBlue,
                'arrowUni' => $this->arrowUni,
                'ammo' => $this->ammo,
                'defence' => $this->defence,
                'devel' => $this->devel,
                'card' => $this->getHandAmount(),
            ],
            'passed' => $this->passed,
            'hand' => $current ? $this->getHand()->toArray() : [],
            'locations' => Locations::getBoardUI($this->id),
            'usedFactionActions' => $this->getUsedFactionActions(),
            'dealsResources' => Locations::getDealsResources($this->id),
            'connections' => $current ? Connections::getInLocation([LOCATION_HAND, $this->id])->toArray() : [],
        ];
        return $data;
    }
}
