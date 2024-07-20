<?php

namespace STATE\Data\Connections;

use STATE\Models\Connection;

class Thugs extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_THUGS;
        $this->name = clienttranslate("Thugs");
        $this->spendRequirements = [RESOURCE_GUN];
        $this->buildingBonus = [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED, RESOURCE_ARROW_RED];
        $this->copies = 2;
    }
}
