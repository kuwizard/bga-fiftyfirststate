<?php

namespace STATE\Managers;

use STATE\Helpers\Collection;
use STATE\Helpers\DB_Manager;
use STATE\Helpers\ResourcesHelper;
use STATE\Models\Faction;

class Factions extends DB_Manager
{
    protected static $table = 'factions';
    protected static $primary = 'id';

    private static $factionsNames = [
        FACTION_NEW_YORK => 'NewYork',
        FACTION_APPALACHIAN => 'Appalachian',
        FACTION_MUTANTS => 'Mutants',
        FACTION_MERCHANTS => 'Merchants',
    ];

    public static function setupNewGame($players)
    {
        $values = [];
        foreach ($players as $player) {
            $faction = $player->getFaction();
            foreach ($player->getFactionActions() as $id => $factionAction) {
                $values[] = [
                    'faction' => $faction,
                    'action_number' => $id,
                ];
            }
        }

        if (!empty($values)) {
            self::DB()
                ->multipleInsert(['faction', 'action_number'])
                ->values($values);
        }
    }

    /**
     * @param int $faction
     * @param int $number
     * @return array
     */
    public static function getAllForFaction($faction)
    {
        return self::DB()
            ->where('faction', $faction)
            ->get()
            ->toArray();
    }

    /**
     * @param int $faction
     * @param int $number
     * @return array
     */
    public static function setAsUsed($faction, $number)
    {
        return self::DB()
            ->update(['used' => 1])
            ->where('faction', $faction)
            ->where('action_number', $number)
            ->run();
    }

    public static function resetAllUsed()
    {
        return self::DB()
            ->update(['used' => 0])
            ->run();
    }

    /**
     * @param int $type
     * @return string
     */
    public static function getName($type)
    {
        return self::$factionsNames[$type];
    }

    public static function getAllProductionsAndActionsUI()
    {
        return array_map('self::getProductionAndActionsUI', array_keys(self::$factionsNames));
    }

    private static function getProductionAndActionsUI($type)
    {
        $name = "STATE\Data\Factions\\" . self::$factionsNames[$type];
        /** @var Faction $faction */
        $faction = new $name();
        $production = $faction->getProduction();
        $uniqueResource = array_values(array_diff($production, [RESOURCE_WORKER, RESOURCE_ARROW_GREY, RESOURCE_CARD]))[0];
        $order = [RESOURCE_WORKER, $uniqueResource, RESOURCE_ARROW_GREY, RESOURCE_CARD];
        return [
            'production' => array_count_values(ResourcesHelper::getResourceNames($production)),
            'order' => ResourcesHelper::getResourceNames($order),
        ];
    }

    public static function getAll(): array
    {
        return array_keys(self::$factionsNames);
    }
}
