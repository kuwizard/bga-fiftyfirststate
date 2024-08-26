<?php

namespace STATE\Managers;

use STATE\Helpers\DB_Manager;

class Resources extends DB_Manager
{
    protected static $table = 'resources';
    protected static $primary = 'id';

    /**
     * @param int $locationId
     * @param int[] $resources
     * @return void
     */
    public static function place($locationId, $resources)
    {
        $values = [];
        foreach ($resources as $resource) {
            $values[] = [
                'location_id' => $locationId,
                'type' => $resource,
            ];
        }
        self::DB()
            ->multipleInsert(['location_id', 'type'])
            ->values($values);
    }

    /**
     * @param int $locationId
     * @return int[]
     */
    public static function get($locationId)
    {
        return self::DB()
            ->select(['type'])
            ->where('location_id', $locationId)
            ->get()
            ->map(function ($resource) {
                return (int) $resource['type'];
            })
            ->toArray();
    }
}
