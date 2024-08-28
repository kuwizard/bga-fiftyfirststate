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
     * @param int $resource
     * @return void
     */
    public static function add($locationId, $resource)
    {
        self::DB()
            ->insert(['location_id' => $locationId, 'type' => $resource]);
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

    public static function getMultiple(array $ids)
    {
        return self::DB()
            ->select(['type'])
            ->whereIn('location_id', $ids)
            ->get()
            ->map(function ($resource) {
                return (int) $resource['type'];
            })
            ->toArray();
    }

    /**
     * @param int $locationId
     * @param int $resource
     * @return void
     */
    public static function delete($locationId, $resource)
    {
        self::DB()
            ->deleteSingle()
            ->where('location_id', $locationId)
            ->where('type', $resource)
            ->run();
    }

    /**
     * @param int $resource
     * @return int[]
     */
    public static function getLocationIdsByResource($resource)
    {
        return array_unique(
            self::DB()
                ->select(['location_id'])
                ->where('type', $resource)
                ->get()
                ->map(function ($resource) {
                    return (int) $resource['location_id'];
                })
                ->toArray()
        );
    }
}
