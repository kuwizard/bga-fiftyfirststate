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
     * @var Action[]
     */
    protected $actions;

    public function __construct()
    {
        $this->resources = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_CARD, RESOURCE_ARROW_GREY];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

//    public function jsonSerialize()
//    {
//        return [
//            'id' => $this->id,
//        ];
//    }
}
