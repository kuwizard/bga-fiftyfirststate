<?php

namespace STATE\Models;


use STATE\Managers\Resources;

class FeatureStorageSingle extends FeatureStorage
{
    /**
     * @var int
     */
    protected $resourceType;
    /**
     * @var int
     */
    protected $resourceLimit;

    /**
     * @param int $amount
     * @return void
     */
    public function placeResourcesOneType($type, $amount)
    {
        $resources = array_fill(0, $amount, $type);
        Resources::place($this->id, $resources);
        $this->resources = $resources;
    }

    public function getResourceLimit(): int
    {
        return $this->resourceLimit;
    }

    public function getResourceType(): int
    {
        return $this->resourceType;
    }
}
