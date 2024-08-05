<?php

namespace STATE\Models;

use JsonSerializable;
use STATE\Core\Preferences;
use STATE\Helpers\DB_Manager;
use STATE\Managers\Locations;
use STATE\Managers\Players;

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

    private function getResourceName($type)
    {
        return [
            RESOURCE_FUEL => 'fuel',
            RESOURCE_GUN => 'gun',
            RESOURCE_IRON => 'iron',
            RESOURCE_BRICK => 'brick',
            RESOURCE_WORKER => 'worker',
            RESOURCE_ARROW_GREY => 'arrowGrey',
            RESOURCE_ARROW_RED => 'arrowRed',
            RESOURCE_ARROW_BLUE => 'arrowBlue',
            RESOURCE_ARROW_UNIVERSAL => 'arrowUni',
            RESOURCE_AMMO => 'ammo',
            RESOURCE_DEFENCE => 'defence',
            RESOURCE_DEVELOPMENT => 'devel',
            RESOURCE_CARD => 'cards',
        ][$type];
    }

    private function getDBName($type)
    {
        if (in_array($type, [RESOURCE_ARROW_GREY, RESOURCE_ARROW_RED, RESOURCE_ARROW_BLUE, RESOURCE_ARROW_UNIVERSAL])) {
            return 'player_' . [
                    RESOURCE_ARROW_GREY => 'arrow_grey',
                    RESOURCE_ARROW_RED => 'arrow_red',
                    RESOURCE_ARROW_BLUE => 'arrow_blue',
                    RESOURCE_ARROW_UNIVERSAL => 'arrow_uni',
                ][$type];
        } else {
            return 'player_' . $this->getResourceName($type);
        }
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
     * @return int
     */
    public function getHandAmount()
    {
        return Locations::countInLocation([LOCATION_HAND, $this->id]);
    }

    public function getHand()
    {
        return Locations::getInLocation([LOCATION_HAND, $this->id])->toArray();
    }

    /**
     * @param int[] $array_values
     * @return array
     */
    public function getResourcesWithNames($resources)
    {
        $result = [];
        foreach ($resources as $resource) {
            $resourceName = $this->getResourceName($resource);
            $value = $resource === RESOURCE_CARD ? $this->getHandAmount() : $this->$resourceName;
            $result[$resourceName] = $value;
        }
        return $result;
    }

    /**
     * @param array $resources
     * @return void
     */
    public function increaseResources($resources)
    {
        if (isset($resources[RESOURCE_CARD])) {
            Locations::draw($this, $resources[RESOURCE_CARD]);
            unset($resources[RESOURCE_CARD]);
        }
        foreach ($resources as $type => $amount) {
            $this->increaseResource($type, $amount);
        }
    }

    public function increaseResource($type, $amount = 1)
    {
        $resourceName = $this->getResourceName($type);
        $newAmount = $this->{$resourceName} + $amount;
        $this->{$resourceName} = $newAmount;
        $this->updateResource($this->getDBName($type), $newAmount);
    }

    public function decreaseResource($type, $amount = 1)
    {
        $resourceName = $this->getResourceName($type);
        $resourceAmount = $this->{$resourceName};
        $newAmount = $resourceAmount - $amount;
        if ($newAmount < 0) {
            throw new \BgaVisibleSystemException(
                "Something's wrong. You try to decrease resource {$resourceName} to a negative value. You have {$resourceAmount}, amount to lose - {$amount}"
            );
        }
        $this->{$resourceName} = $newAmount;
        $this->updateResource($this->getDBName($type), $newAmount);
    }

    private function updateResource($name, $amount)
    {
        self::DB()
            ->update([$name => $amount])
            ->wherePlayer($this->id)
            ->run();
    }

    public function discard($cardIds)
    {
        Locations::discard($cardIds);
    }

    public function drawCards($amount = 1)
    {
        Locations::draw($this, $amount);
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
                $resources = $locationCard->getProduct();
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

    public function jsonSerialize($currentPlayerId = null)
    {
        $current = $this->id === $currentPlayerId;
        $data = [
            'id' => $this->id,
            'no' => $this->no,
            'name' => $this->name,
            'color' => $this->color,
            'score' => $this->score,
            'faction' => $this->faction,
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
            'passed' => $this->passed,
            'handAmount' => $this->getHandAmount(),
            'hand' => $current ? Locations::getHand($this->id) : [],
            'locations' => Locations::getBoard($this->id),
        ];
        return $data;
    }
}
