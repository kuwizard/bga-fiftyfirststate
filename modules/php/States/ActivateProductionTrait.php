<?php

namespace Bga\Games\Fiftyfirststate\States;

use Bga\Games\Fiftyfirststate\Core\Globals;
use Bga\Games\Fiftyfirststate\Core\Stack;
use Bga\Games\Fiftyfirststate\Managers\Locations;
use Bga\Games\Fiftyfirststate\Managers\Players;
use Bga\Games\Fiftyfirststate\Models\Production;

trait ActivateProductionTrait
{
    public function argActivateProduction()
    {
        $args = [];
        $player = Players::getActive();
        /** @var Production $location */
        foreach ($player->getProductionLocations() as $location) {
            $args[$location->getId()] = in_array(RESOURCE_CARD, $location->getProduct($player));
        }
        return [
            'locations' => $args,
            'willPlayNextTurn' => Globals::willPlayNextTurn(),
        ];
    }

    public function actActivateProduction(int $id)
    {
        $player = Players::getActive();
        if (!in_array($id, $player->getProductionLocations()->getIds())) {
            throw new \BgaVisibleSystemException('Cannot activate a Production with id ' . $id);
        }
        $productionResources = Locations::get($id)->getProduct($player);
        self::giveExtraTime($player->getId());
        Stack::insertOnTopAndFinish(
            ST_CREATE_RESOURCE_SOURCE_MAP,
            [
                'spend' => [RESOURCE_WORKER, RESOURCE_WORKER],
                'bonus' => $productionResources,
                'activatorId' => Stack::getCtx()['productionManagerId'],
            ]
        );
    }
}
