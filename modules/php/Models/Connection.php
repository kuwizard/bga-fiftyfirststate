<?php

namespace STATE\Models;

class Connection implements \JsonSerializable
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
     * @var int[]
     */
    protected $spendRequirements;
    /**
     * @var int[]
     */
    protected $buildingBonus;
    /**
     * @var int
     */
    protected $copies;

    public function __construct($params = [])
    {
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->spendRequirements = [];
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

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
        ];
    }
}
