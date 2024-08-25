<?php

namespace STATE\Models;

class Feature extends Location
{
    /**
     * @var int
     */
    protected $featureType;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->featureType = FEATURE_NONE;
    }

    /**
     * @return string
     */
    public function getFactionRow()
    {
        return 'feature';
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

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
        ]);
    }
}
