<?php

namespace STATE\Models;

use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Resources;

class Feature extends Location
{
    /**
     * @var int
     */
    protected $featureType;
    /**
     * @var int
     */
    protected $resourceType;
    /**
     * @var int[]
     */
    protected $resources;
    /**
     * @var int
     */
    protected $resourceLimit;
    /**
     * @var int[]
     */
    protected $resourcesOptions;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->featureType = FEATURE_NONE;
        $this->resourceLimit = 0;
        $this->resources = is_null($this->id) ? null : Resources::get($this->id);
        $this->resourcesOptions = [];
    }

    /**
     * @return string
     */
    public function getFactionRow()
    {
        return 'feature';
    }

    public function getFeatureType(): int
    {
        return $this->featureType;
    }

    public function getResourceLimit(): int
    {
        return $this->resourceLimit;
    }

    public function getResourceType(): int
    {
        return $this->resourceType;
    }

    public function getResourcesAmount(): int
    {
        return count($this->resources);
    }

    public function getResourcesUI(): array
    {
        return ResourcesHelper::getResourceNames($this->resources);
    }

    /**
     * @param Player $player
     * @param int $icon
     * @return array
     */
    protected function getVPForEachIcon($player, $icon)
    {
        $icons = $player->getBoardIcons($icon);
        $maxIcons = array_slice($icons, 0, 5);
        return array_fill(0, count($maxIcons), RESOURCE_VP);
    }

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

    public function addResource(int $resource)
    {
        $this->resources[] = $resource;
        Resources::add($this->id, $resource);
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'resources' => ResourcesHelper::getResourceNames($this->resources),
        ]);
    }
}
