<?php

namespace STATE\Models;

use STATE\Core\Notifications;
use STATE\Core\Stack;

class Act implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $type;
    /**
     * @var int[]
     */
    protected $spendRequirements;
    /**
     * @var int[]
     */
    protected $bonus;

    public function __construct($spendRequirements, $bonus, $type = ACTION_TYPE_SPEND)
    {
        $this->spendRequirements = $spendRequirements;
        $this->bonus = $bonus;
        $this->type = $type;
    }

    public function getSpendRequirements(): array
    {
        return $this->spendRequirements;
    }

    public function getSpendRequirementsUI(): array
    {
        return array_map('STATE\Helpers\Resources::getResourceName', $this->spendRequirements);
    }

    public function getSpendRequirementsUIRemoveCard(): array
    {
        $requirements = $this->spendRequirements;
        if (in_array(RESOURCE_CARD, $requirements)) {
            $requirements = array_diff($requirements, [RESOURCE_CARD]);
        }
        return array_map('STATE\Helpers\Resources::getResourceName', $requirements);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function activate($player)
    {
        $resourcesChanged = [];
        $spendRequirements = $this->spendRequirements;
        if (in_array(RESOURCE_CARD, $spendRequirements)) {
            Stack::insertOnTop(ST_DISCARD_LOCATION_FOR_RESOURCES);
            $spendRequirements = array_diff($spendRequirements, [RESOURCE_CARD]);
        }
        foreach (array_count_values($spendRequirements) as $spendRequirement => $amount) {
            $player->decreaseResource($spendRequirement, $amount);
            $resourcesChanged[] = $spendRequirement;
        }
        foreach (array_count_values($this->bonus) as $bonus => $amount) {
            $player->increaseResource($bonus, $amount);
            $resourcesChanged[] = $bonus;
        }
        Notifications::resourcesChanged($player, $player->getResourcesWithNames($resourcesChanged));
    }

    public function jsonSerialize()
    {
        return [
            'spendRequirements' => $this->getSpendRequirementsUI(),
            'bonus' => array_map('STATE\Helpers\Resources::getResourceName', $this->bonus),
        ];
    }
}
