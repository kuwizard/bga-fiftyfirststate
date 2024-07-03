<?php

namespace STATE\Models;

use JsonSerializable;
use STATE\Core\Preferences;
use STATE\Helpers\DB_Manager;

/*
 * Player: all utility functions concerning a player
 */

class Player extends DB_Manager implements JsonSerializable
{
    protected static $table = 'player';
    protected static $primary = 'player_id';

    protected $id;
    protected $no; // natural order
    protected $name; // player name
    protected $color;
    protected $eliminated = false;
    protected $score = 0;
    protected $zombie = false;
    protected $multiactive;

    public function __construct($row)
    {
        if ($row != null) {
            $this->id = (int)$row['player_id'];
            $this->no = (int)$row['player_no'];
            $this->name = $row['player_name'];
            $this->color = $row['player_color'];
            $this->eliminated = $row['player_eliminated'] == 1;
            $this->score = (int)$row['player_score'];
            $this->zombie = $row['player_zombie'] == 1;
            $this->multiactive = $row['player_is_multiactive'] === '1';
        }
    }

    /*
     * Getters
     */
    public function getId()
    {
        return $this->id;
    }

    public function getNo()
    {
        return $this->no;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function isZombie()
    {
        return $this->zombie;
    }

    public function getPref($prefId)
    {
        return Preferences::get($this->id, $prefId);
    }

    public function jsonSerialize($currentPlayerId = null)
    {
        $data = [
            'id' => $this->id,
            'no' => $this->no,
            'name' => $this->name,
            'color' => $this->color,
            'score' => $this->score,
            'multiactive' => $this->multiactive,
        ];
        return $data;
    }
}
