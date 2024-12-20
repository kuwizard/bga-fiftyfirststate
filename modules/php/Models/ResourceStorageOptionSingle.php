<?php

namespace STATE\Models;

class ResourceStorageOptionSingle
{
    protected int $resource;
    protected int $limit;

    public function __construct($resource, $limit)
    {
        $this->resource = $resource;
        $this->limit = $limit;
    }

    public function getResource(): int
    {
        return $this->resource;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
