<?php

namespace STATE\Core;

use STATE\Helpers\DB_Manager;

/*
 * User preferences
 */

class Preferences extends DB_Manager
{
    protected static $table = 'user_preferences';
    protected static $primary = 'id';
    protected static $log = false; // Turn off log to avoid undoing changes in this table

    protected static function cast($row)
    {
        return $row;
    }

    /*
     * Setup new game
     */
    public static function setupNewGame($players, $prefs)
    {
        // Load user preferences
        $fileName = dirname(__FILE__) . '/../../../gamepreferences.json';
        $file = fopen($fileName, 'r');
        $json = fread($file, filesize($fileName));

        // Decode the JSON file
        $json_data = json_decode($json, true);
        foreach ($json_data as $id => $data) {
            if ($id !== 200) { // BGA injects their own id in gamepreferences.json
                $defaultValue = $data['default'] ?? -1;

                foreach ($players as $pId => $infos) {
                    $values[] = [
                        'player_id' => $pId,
                        'pref_id' => (int) $id,
                        'pref_value' => $prefs[$pId][$id] ?? $defaultValue,
                    ];
                }
            }
        }

        if (!empty($values)) {
            self::DB()
                ->multipleInsert(['player_id', 'pref_id', 'pref_value'])
                ->values($values);
        }
    }

    /*
     * Get a user preference
     */
    public static function get($pId, $prefId)
    {
        return self::DB()
            ->select(['pref_value'])
            ->where('player_id', $pId)
            ->where('pref_id', $prefId)
            ->get(true)['pref_value'] ?? null;
    }

    /*
     * Set a user preference
     */
    public static function set($pId, $prefId, $value)
    {
        return self::DB()
            ->update(['pref_value' => $value])
            ->where('player_id', $pId)
            ->where('pref_id', $prefId)
            ->run();
    }
}
