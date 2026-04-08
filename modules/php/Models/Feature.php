<?php

namespace Bga\Games\Fiftyfirststate\Models;

class Feature extends Location
{
    /**
     * @return string
     */
    public function getFactionRow()
    {
        return 'feature';
    }

    public function getFactionRowName(): string
    {
        return clienttranslate('Feature');
    }

    public function getRowText(): string
    {
        return clienttranslate('FEATURE');
    }

    public function getDefenceValue(): int
    {
        return 4 + parent::getDefenceValue();
    }

    protected function getVPForEachIcon(?Player $player, int $icon): array
    {
        $icons = $player ? $player->getBoardIcons($icon) : [];
        $maxIcons = array_slice($icons, 0, 5);
        return array_fill(0, count($maxIcons), RESOURCE_VP);
    }
}
