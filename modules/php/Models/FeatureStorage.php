<?php

namespace STATE\Models;

use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Resources;

class FeatureStorage extends Feature
{
    /**
     * @var int[]
     */
    protected $resources;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->resources = is_null($this->id) ? null : Resources::get($this->id);
    }

    public function getResourcesAmount(): int
    {
        return count($this->resources);
    }

    /**
     * @return int[]|null
     */
    public function getResources()
    {
        return $this->resources;
    }

    public function getResourcesUI(): array
    {
        return ResourcesHelper::getResourceNames($this->resources);
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'resources' => ResourcesHelper::getResourceNames($this->resources),
        ]);
    }
}
