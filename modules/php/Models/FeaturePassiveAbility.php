<?php

namespace Bga\Games\Fiftyfirststate\Models;

use Bga\Games\Fiftyfirststate\Core\Notifications;

class FeaturePassiveAbility extends Feature
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->passiveAbilities = [];
    }

    protected array $passiveAbilities;

    public function activatePassiveAbility(Player $player, string $locationAction)
    {
        $resourcesGained = [];
        foreach ($this->passiveAbilities as $trigger => $bonus) {
            if ($locationAction === $trigger) {
                $player->increaseResource($bonus);
                Notifications::passiveAbilityApplied($player, $this, $bonus);
                $resourcesGained[] = $bonus;
            }
        }
        return $resourcesGained;
    }

    public function isTriggeredBy($locationAction)
    {
        return in_array($locationAction, array_keys($this->passiveAbilities));
    }

    public function getBonusFor($locationAction): int|null
    {
        return $this->passiveAbilities[$locationAction] ?? null;
    }
}
