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
    public function placeResources()
    {
        $resources = [];
        /** @var ResourceStorageOptionSingle $resourceOption */
        foreach ($this->resourcesOptions as $resourceOption) {
            $newResources = array_fill(0, $resourceOption->getLimit(), $resourceOption->getResource());
            $resources = array_merge($resources, $newResources);
        }
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
