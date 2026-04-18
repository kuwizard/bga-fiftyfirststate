<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Notifications;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Helpers\ResourcesHelper;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\Player;

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
        Players::getAllNonPassed(Players::getActiveId())->map(function (Player $player) use (&$args, $ctx) {
            $args[] = $this->getResourcesOfPlayer($player, $ctx['resourcesAllowed']);
        });
        return ['players' => $args, 'willPlayNextTurn' => Globals::willPlayNextTurn()];
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
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
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

    public function actChooseResourceToSteal(string $resourceName)
    {
        $ctx = Stack::getCtx();
        $victim = Players::get($ctx['victimId']);
        $this->stealResourceFromPlayer($victim, $resourceName, $ctx['spend'], $ctx['activatorId']);
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
        Notifications::resourceStolen(Players::getActive(), $victim, $resource);
        Stack::insertOnTopAndFinish(
            ST_CREATE_RESOURCE_SOURCE_MAP,
            [
                'spend' => $spend,
                'bonus' => [$resource],
                'activatorId' => $activatorId,
            ]
        );
    }
}
