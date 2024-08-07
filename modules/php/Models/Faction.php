<?php

namespace STATE\Models;

class Faction
{
    /**
     * @var int
     */
    protected $type;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int[]
     */
    protected $resources;
    /**
     * @var Act[]
     */
    protected $actions;

    public function __construct()
    {
        $this->resources = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_CARD, RESOURCE_ARROW_GREY];
    }

    /**
     * @return int[]
     */
    public function getProduction()
    {
        return $this->resources;
    }

    /**
     * @return Act[]
     */
    public function getActions()
    {
        return $this->actions;
    }

//    public function jsonSerialize()
//    {
//        return [
//            'id' => $this->id,
//        ];
//    }
}
