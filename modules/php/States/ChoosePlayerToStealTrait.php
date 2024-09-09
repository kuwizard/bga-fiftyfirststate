<?php

namespace STATE\States;

use STATE\Core\Notifications;
use STATE\Core\Stack;
use STATE\Helpers\ResourcesHelper;
use STATE\Managers\Players;
use STATE\Models\Player;

trait ChoosePlayerToStealTrait
{
    public function argChoosePlayerToSteal()
    {
        $args = [];
        $ctx = Stack::getCtx();
        if ($ctx['state'] === ST_CREATE_RESOURCE_SOURCE_MAP) {
            // BGA framework, why do you ever call that if the state is ST_CREATE_RESOURCE_SOURCE_MAP???
            return [];
        }
        Players::getAll(Players::getActiveId())->map(function (Player $player) use (&$args, $ctx) {
            $args[] = $this->getResourcesOfPlayer($player, $ctx['resourcesAllowed']);
        });
        return $args;
    }

    public function argChooseResourceToSteal()
    {
        $ctx = Stack::getCtx();
        if ($ctx['state'] === ST_CREATE_RESOURCE_SOURCE_MAP) {
            return [];
        }
        $victim = Players::get($ctx['victimId']);
        return [
            'playerId' => $victim->getId(),
            'player_name' => $victim->getName(),
            'resources' => array_values(
                ResourcesHelper::getResourceNames($victim->getResourcesNotZero($ctx['resourcesAllowed']))
            ),
        ];
    }

    /**
     * @param Player $player
     * @return array
     */
    private function getResourcesOfPlayer($player, $resourcesAllowed)
    {
        $resourcesPlayerHas = $player->getResourcesNotZero($resourcesAllowed);
        return [
            'playerId' => $player->getId(),
            'playerName' => $player->getName(),
            'resources' => array_values(ResourcesHelper::getResourceNames($resourcesPlayerHas)),
        ];
    }

    public function actChoosePlayerToSteal(int $pId)
    {
        $ctx = Stack::getCtx();
        Stack::insertOnTopAndFinish(
            ST_CHOOSE_RESOURCE_TO_STEAL,
            [
                'victimId' => $pId,
                'resourcesAllowed' => $ctx['resourcesAllowed'],
                'activatorId' => $ctx['activatorId'],
                'spend' => $ctx['spend'],
            ],
        );
    }

    public function actChooseResourceToSteal(string $resource)
    {
        $ctx = Stack::getCtx();
        $victim = Players::get($ctx['victimId']);
        $this->stealResourceFromPlayer($victim, $resource, $ctx['spend'], $ctx['activatorId']);
    }

    public function actChoosePlayerAndResourceToSteal(string $resource, int $pId)
    {
        $ctx = Stack::getCtx();
        $victim = Players::get($pId);
        $this->stealResourceFromPlayer($victim, $resource, $ctx['spend'], $ctx['activatorId']);
    }


    private function stealResourceFromPlayer(Player $victim, string $resourceName, array $spend, int $activatorId)
    {
        $resource = ResourcesHelper::getResourceType($resourceName);
        $victim->decreaseResource($resource);
        Notifications::resourcesChanged($victim, $victim->getResourcesWithNames([$resource]));
        Stack::insertOnTopAndFinish(
            ST_CREATE_RESOURCE_SOURCE_MAP, [
                'spend' => $spend,
                'bonus' => [$resource],
                'activatorId' => $activatorId,
            ]
        );
    }
}
