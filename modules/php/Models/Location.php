<?php

namespace STATE\Models;

use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Locations;

class Location implements \JsonSerializable
{
    protected int|null $id;
    protected string|null $type;
    protected string $name;
    protected int $distance;
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
    protected int|null $activatedTimes;
    protected bool $isRuined;
    protected int $copies;
    protected array $expansionCopies;
    protected array $text;
    protected bool $isDefended;

    public function __construct($params = [])
    {
        if (isset($params['location_id'])) {
            // TODO: Find out why it could be id in some cases (getAllDatas) and location_id at others (actUseLocation)
            $params['id'] = $params['location_id'];
        }
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->type = $params['type'] ?? null;
        $this->buildingBonus = [];
        $this->activatedTimes = isset($params['activated_times']) ? (int) $params['activated_times'] : null;
        $this->isRuined = isset($params['is_ruined']) && (int) $params['is_ruined'] === 1 ?? false;
        $this->copies = 1;
        $this->expansionCopies = [
            NEW_ERA => 0,
        ];
        $this->text = [];
        $this->isDefended = isset($params['is_defended']) && (int) $params['is_defended'] === 1;
    }

    protected function getText(bool $isBuildingBonus = false): array
    {
        return [
            TEXT_TYPE => $this->getRowText(),
            TEXT_DESCRIPTION => '',
            TEXT_BUILDING_BONUS => empty($this->getBuildingBonus()) && !$isBuildingBonus ? '' : clienttranslate('BUILDING BONUS'),
            TEXT_BONUS_DESCRIPTION => '',
            TEXT_MAY_BE_ACTIVATED_TWICE => '',
        ];
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

    public function getCopies(): int
    {
        return $this->copies;
    }

    public function getExpansionCopies(): array
    {
        return $this->expansionCopies;
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
     * @return int[]
     */
    public function getBuildingBonus(Player $player = null): array
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

    public function getFactionRowName(): string
    {
        return '';
    }

    public function getRowText(): string
    {
        return '';
    }

    public function getSpendRequirements(): array
    {
        return [];
    }

    public function getDefenceValue(): int
    {
        return $this->isDefended ? 1 : 0;
    }

    public function isRuined(): bool
    {
        return $this->isRuined;
    }

    public function isDefended(): bool
    {
        return $this->isDefended;
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
            'text' => $this->text,
            'expansion' => Locations::getExpansion($this->type),
            'isDefended' => $this->isDefended,
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
