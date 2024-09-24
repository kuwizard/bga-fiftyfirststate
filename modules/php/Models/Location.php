<?php

namespace STATE\Models;

use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;

class Location implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected $distance;
    /**
     * @var int[]
     */
    protected $spoils;
    /**
     * @var int[]
     */
    protected $icons;
    /**
     * @var int[]
     */
    protected $buildingBonus;
    /**
     * @var int[]
     */
    protected $deals;
    /**
     * @var int
     */
    protected $activationsMax;
    /**
     * @var int
     */
    protected $activatedTimes;
    /**
     * @var bool
     */
    protected $isRuined;
    /**
     * @var int
     */
    protected $copies;

    public function __construct($params = [])
    {
        if (isset($params['location_id'])) {
            // TODO: Find out why it could be id in some cases (getAllDatas) and location_id at others (actUseLocation)
            $params['id'] = $params['location_id'];
        }
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->type = $params['type'] ?? null;
        $this->buildingBonus = [];
        $this->activationsMax = 1;
        $this->activatedTimes = isset($params['activated_times']) ? (int) $params['activated_times'] : null;
        $this->isRuined = isset($params['is_ruined']) && (int) $params['is_ruined'] === 1 ?? false;
        $this->copies = 1;
    }

    /**
     * @return int | null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCopies()
    {
        return $this->copies;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int[]
     */
    public function getDeals()
    {
        return $this->deals;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function getSpoils(): array
    {
        return $this->spoils;
    }

    /**
     * @param Player $player
     * @return int[]
     */
    public function getBuildingBonus($player)
    {
        return $this->buildingBonus;
    }

    /**
     * @return int[]
     */
    public function getIcons(): array
    {
        return $this->icons;
    }

    /**
     * @return string
     */
    public function getFactionRow()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getFactionRowName()
    {
        return '';
    }

    /**
     * @return int[]
     */
    public function getSpendRequirements()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getDefenceValue()
    {
        return 0;
    }

    /**
     * @return bool
     */
    public function isRuined()
    {
        return $this->isRuined;
    }

    public function ruin()
    {
        $this->isRuined = true;
        Locations::ruin($this);
    }

    public function unruin()
    {
        $this->isRuined = false;
        Locations::unruin($this);
    }

    public function activate($player)
    {
    }

    public function postActivation()
    {
    }

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id,
            'sprite' => Locations::getSprite($this->type),
            'isRuined' => $this->isRuined,
            'name' => $this->name,
        ];
        if (!$this->isRuined && $this->activatedTimes > 0) {
            $requirements = $this->getSpendRequirements();
            if (in_array(RESOURCE_DEAL, $requirements)) {
                $requirements = array_values(array_diff($requirements, [RESOURCE_DEAL]));
            }
            $requirementsNames = ResourcesHelper::getResourceNames($requirements);
            $requirementsSingle = $requirementsNames;
            for ($i = 0; $i < $this->activatedTimes - 1; $i++) {
                $requirementsNames = array_merge($requirementsNames, $requirementsSingle);
            }
            $data['resources'] = $requirementsNames;
        }
        return $data;
    }
}
