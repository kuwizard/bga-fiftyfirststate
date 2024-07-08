<?php

namespace STATE\Data\Cards;

use STATE\Models\ProductionCard;

class BrickSupplier extends ProductionCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BRICK_SUPPLIER;
        $this->name = clienttranslate("Brick Supplier");
        $this->distance = 2;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK, ICON_AMMO];
        $this->deals = [RESOURCE_BRICK];
        $this->product = [RESOURCE_BRICK]; // TODO: For each brick icon
        $this->copies = 2;
    }
}
