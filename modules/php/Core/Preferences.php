<?php

namespace STATE\Core;

use STATE\Helpers\DB_Manager;
use STATE\Managers\Players;

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
        $values = [];
        // Read the JSON file
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
        $valueInDb = self::get($pId, $prefId);
        if (is_null($valueInDb)) {
            return self::DB()
                ->insert(['pref_value' => $value, 'player_id' => $pId, 'pref_id' => $prefId]);
        } else {
            return self::DB()
                ->update(['pref_value' => $value])
                ->where('player_id', $pId)
                ->where('pref_id', $prefId)
                ->run();
        }
    }

    public static function setPreferences(int $pId, array $factions, array $sides)
    {
        $idPrefMap = array_combine([NEW_YORK_PREFERENCE, APPALACHIAN_PREFERENCE, MUTANTS_PREFERENCE, MERCHANTS_PREFERENCE],
            $factions);
        foreach ($idPrefMap as $prefId => $factionPref) {
            self::set($pId, $prefId, $factionPref);
        }
        $idSideMap = array_combine([NEW_YORK_SIDE, APPALACHIAN_SIDE, MUTANTS_SIDE, MERCHANTS_SIDE], $sides);
        foreach ($idSideMap as $prefId => $side) {
            self::set($pId, $prefId, $side);
        }
    }

    /**
     * @param int $pId
     * @return int[]
     */
    public static function getColorPreferencesSingle($pId)
    {
        $data = self::DB()
            ->select(['pref_value'])
            ->where('player_id', $pId)
            ->orderBy('pref_id')
            ->get()
            ->toArray();
        return array_map(function ($value) {
            return (int) $value['pref_value'];
        }, $data);
    }

    /**
     * @param int $pId
     * @return int[]
     */
    public static function getPreferencesAll()
    {
        $result = [];
        foreach (
            self::DB()
                ->select(['player_id', 'pref_id', 'pref_value'])
                ->orderBy('player_id, pref_id')
                ->get()
                ->toArray()
            as $item
        ) {
            $result[$item['player_id']][$item['pref_id']] = $item['pref_value'];
        }
        return $result;
    }
}
