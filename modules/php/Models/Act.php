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

    public function getSpendRequirementsUIRemoveCard(): array
    {
        $requirements = $this->spendRequirements;
        if (in_array(RESOURCE_CARD, $requirements)) {
            $requirements = array_diff($requirements, [RESOURCE_CARD]);
        }
        return ResourcesHelper::getResourceNames($requirements);
    }

    public function activate()
    {
        $spendRequirements = $this->spendRequirements;
        if (in_array(RESOURCE_CARD, $spendRequirements)) {
            Stack::insertOnTop(ST_DISCARD_LOCATION_FOR_RESOURCES);
            $spendRequirements = array_diff($spendRequirements, [RESOURCE_CARD]);
        }
        Stack::insertOnTop(ST_CHOOSE_RESOURCE_SOURCE, [
            'spend' => $spendRequirements,
            'bonus' => $this->bonus,
        ]);
    }

    public function jsonSerialize()
    {
        return [
            'spendRequirements' => $this->getSpendRequirementsUI(),
            'bonus' => ResourcesHelper::getResourceNames($this->bonus),
        ];
    }
}
