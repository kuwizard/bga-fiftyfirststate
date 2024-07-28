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
    /**
     * @var int
     */
    protected $activatedTimes;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->activateTimes = 1;
        $this->activatedTimes = isset($params['activatedTimes']) ? (int) $params['activatedTimes'] : null;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function action($player)
    {

    }
}
