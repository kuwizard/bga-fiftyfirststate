<?php

namespace STATE\Models;

use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;

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
        return ResourcesHelper::getResourceNames($this->spendRequirements);
    }

    public function getBonusUi(): array
    {
        return ResourcesHelper::getResourceNames($this->bonus);
    }

    public function getSpendRequirementsUIRemoveCard(): array
    {
        $requirements = $this->spendRequirements;
        if (in_array(RESOURCE_CARD, $requirements)) {
            $requirements = array_diff($requirements, [RESOURCE_CARD]);
        }
        return ResourcesHelper::getResourceNames($requirements);
    }

    /**
     * @param int | null $activatorId
     * @return void
     */
    public function activate($activatorId)
    {
        switch ($this->type) {
            case ACTION_TYPE_SPEND:
                $spendRequirements = $this->spendRequirements;
                $discardCard = in_array(RESOURCE_CARD, $spendRequirements);
                if ($discardCard) {
                    $spendRequirements = array_diff($spendRequirements, [RESOURCE_CARD]);
                }
                $discardDeal = in_array(RESOURCE_DEAL, $spendRequirements);
                if ($discardDeal) {
                    $spendRequirements = array_diff($spendRequirements, [RESOURCE_DEAL]);
                }
                Stack::insertOnTop(ST_CREATE_RESOURCE_SOURCE_MAP, [
                    'spend' => $spendRequirements,
                    'bonus' => $this->bonus,
                    'activatorId' => $activatorId,
                ]);
                if ($discardCard) {
                    Stack::insertOnTop(ST_DISCARD_LOCATION_FOR_RESOURCES);
                }
                if ($discardDeal) {
                    Stack::insertOnTop(ST_CHOOSE_DEAL_TO_LOSE);
                }
                break;
            case ACTION_TYPE_STEAL_ANOTHER_PLAYER:
                Stack::insertOnTop(ST_CHOOSE_PLAYER_TO_STEAL, [
                    'spend' => $this->spendRequirements,
                    'resourcesAllowed' => $this->bonus,
                    'activatorId' => $activatorId,
                ]);
                break;
        }
    }

    public function jsonSerialize()
    {
        return [
            'spendRequirements' => $this->getSpendRequirementsUI(),
            'bonus' => ResourcesHelper::getResourceNames($this->bonus),
        ];
    }
}
