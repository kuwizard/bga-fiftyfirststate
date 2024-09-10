<?php

namespace STATE\Models;

class Feature extends Location
{
    /**
     * @return string
     */
    public function getFactionRow()
    {
        return 'feature';
    }

    public function getDefenceValue()
    {
        return 4;
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
}
