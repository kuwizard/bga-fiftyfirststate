<?php

namespace STATE\Models;

class ResourceStorageOption
{
    /**
     * @var int[]
     */
    protected $resources;
    /**
     * @var int
     */
    protected $limit;


    public function __construct($resources, $limit)
    {
        $this->resources = $resources;
        $this->limit = $limit;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
