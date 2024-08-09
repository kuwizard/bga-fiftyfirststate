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
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->type = $params['type'] ?? null;
        $this->buildingBonus = [];
        $this->copies = 1;
    }

    /**
     * @return int
     */
    public function getId(): int
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

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'sprite' => Locations::getSprite($this->type),
        ];
    }
}
