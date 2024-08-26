<?php

namespace STATE\Models;

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
    protected $resourceStartAmount;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->featureType = FEATURE_NONE;
        $this->resourceStartAmount = 0;
        $this->resources = Resources::get($this->id);
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

    public function getResourceStartAmount(): int
    {
        return $this->resourceStartAmount;
    }

    public function getResourceType(): int
    {
        return $this->resourceType;
    }

    public function getResourcesUI(): array
    {
        return array_map('STATE\Helpers\ResourcesHelper::getResourceName', $this->resources);
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

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'resources' => array_map('STATE\Helpers\ResourcesHelper::getResourceName', $this->resources),
        ]);
    }
}
