<?php

namespace STATE\Models;

use STATE\Helpers\Resources;
use STATE\Managers\Locations;

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
     * @var int
     */
    protected $resourceAmount;
    /**
     * @var int
     */
    protected $resourceStartAmount;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->featureType = FEATURE_NONE;
        $this->resourceAmount = (int) $params['resource_amount'] ?? null;
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
    public function placeResources($amount)
    {
        Locations::updateResources($this->id, $amount);
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'resourceType' => Resources::getResourceName($this->resourceType),
            'resourceAmount' => $this->resourceAmount,
        ]);
    }
}
