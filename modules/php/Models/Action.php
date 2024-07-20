<?php

namespace STATE\Models;

class Action extends Location
{
    /**
     * @var Act
     */
    protected $action;
    /**
     * @var int
     */
    protected $activateTimes;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->activateTimes = 1;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function action($player)
    {

    }
}
