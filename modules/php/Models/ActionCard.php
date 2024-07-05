<?php

namespace STATE\Models;

class ActionCard extends LocationCard
{
    /**
     * @var int[]
     */
    protected $spendRequirements;
    /**
     * @var int[]
     */
    protected $actionBonus;

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
