<?php

namespace STATE\Models;

use STATE\Core\Notifications;

class Act implements \JsonSerializable
{
    /**
     * @var int|null
     */
    protected $id;
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
        $this->id = null;
        $this->spendRequirements = $spendRequirements;
        $this->bonus = $bonus;
        $this->type = $type;
    }

    public function getSpendRequirements(): array
    {
        return $this->spendRequirements;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function activate($player)
    {
        $resourcesChanged = [];
        foreach (array_count_values($this->spendRequirements) as $spendRequirement => $amount) {
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
            'id' => $this->id,
            'spendRequirements' => array_map('STATE\Helpers\Resources::getResourceName', $this->spendRequirements),
            'bonus' => array_map('STATE\Helpers\Resources::getResourceName', $this->bonus),
        ];
    }
}
