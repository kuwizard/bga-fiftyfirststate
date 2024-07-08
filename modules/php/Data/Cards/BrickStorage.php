<?php

namespace STATE\Data\Cards;

use STATE\Models\FeatureCard;

class BrickStorage extends FeatureCard
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BRICK_STORAGE;
        $this->name = clienttranslate("Brick Storage");
        $this->distance = 1;
        $this->spoils = [RESOURCE_BRICK, RESOURCE_BRICK];
        $this->icons = [ICON_BRICK];
        $this->buildingBonus = []; // TODO: place 3 bricks here
        $this->deals = [RESOURCE_BRICK];
        $this->copies = 1;
    }
}
