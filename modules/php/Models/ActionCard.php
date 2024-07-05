<?php

namespace STATE\Models;

class ActionCard extends LocationCard
{
    /**
     * @var Action
     */
    protected $action;

    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function action($player)
    {

    }
}
