<?php

namespace STATE\Helpers;

class ResourcesHelper
{
    static $mapping = [
        RESOURCE_FUEL => 'fuel',
        RESOURCE_GUN => 'gun',
        RESOURCE_IRON => 'iron',
        RESOURCE_BRICK => 'brick',
        RESOURCE_WORKER => 'worker',
        RESOURCE_ARROW_GREY => 'arrowGrey',
        RESOURCE_ARROW_RED => 'arrowRed',
        RESOURCE_ARROW_BLUE => 'arrowBlue',
        RESOURCE_ARROW_UNIVERSAL => 'arrowUni',
        RESOURCE_AMMO => 'ammo',
        RESOURCE_DEFENCE => 'defence',
        RESOURCE_DEVELOPMENT => 'devel',
        RESOURCE_CARD => 'card',
        RESOURCE_VP => 'score',
    ];

    /**
     * @param int $type
     * @return string
     */
    public static function getResourceName($type)
    {
        return self::$mapping[$type];
    }

    /**
     * @param int[] $types
     * @return string[]
     */
    public static function getResourceNames($types)
    {
        return array_map('self::getResourceName', $types);
    }

    /**
     * @param string $resourceName
     * @return int
     */
    public static function getResourceType($resourceName)
    {
        return array_flip(self::$mapping)[$resourceName];
    }

    /**
     * @param int $type
     * @return string
     */
    public static function getDBName($type)
    {
        if (in_array($type, [RESOURCE_ARROW_GREY, RESOURCE_ARROW_RED, RESOURCE_ARROW_BLUE, RESOURCE_ARROW_UNIVERSAL])) {
            return 'player_' . [
                    RESOURCE_ARROW_GREY => 'arrow_grey',
                    RESOURCE_ARROW_RED => 'arrow_red',
                    RESOURCE_ARROW_BLUE => 'arrow_blue',
                    RESOURCE_ARROW_UNIVERSAL => 'arrow_uni',
                ][$type];
        } else {
            return 'player_' . self::getResourceName($type);
        }
    }
}
