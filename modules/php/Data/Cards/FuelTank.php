<?php

namespace STATE\Data\Cards;

use STATE\Models\LocationCard;

class FuelTank extends LocationCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_FUEL_TANK;
        $this->copies = 2;
    }
}
