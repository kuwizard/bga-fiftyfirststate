<?php

namespace STATE\Models;

use STATE\Managers\Locations;

class Location implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected $distance;
    /**
     * @var int[]
     */
    protected $spoils;
    /**
     * @var int[]
     */
    protected $icons;
    /**
     * @var int[]
     */
    protected $buildingBonus;
    /**
     * @var int[]
     */
    protected $deals;
    /**
     * @var int
     */
    protected $copies;

    public function __construct($params = [])
    {
        if (isset($params['location_id'])) {
            // TODO: Find out why it could be id in some cases (getAllDatas) and location_id at others (actUseLocation)
            $params['id'] = $params['location_id'];
        }
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->type = $params['type'] ?? null;
        $this->buildingBonus = [];
        $this->copies = 1;
    }

    /**
     * @return int | null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCopies()
    {
        return $this->copies;
    }

    /**
     * @return int[]
     */
    public function getDeals()
    {
        return $this->deals;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function getSpoils(): array
    {
        return $this->spoils;
    }

    public function getBuildingBonus()
    {
        return $this->buildingBonus;
    }

    /**
     * @return string
     */
    public function getFactionRow()
    {
        return '';
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'sprite' => Locations::getSprite($this->type),
        ];
    }
}
