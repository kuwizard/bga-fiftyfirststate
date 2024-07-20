<?php

namespace STATE\Data\Connections;

use STATE\Models\Connection;

class JunkTrain extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_JUNK_TRAIN;
        $this->name = clienttranslate("Junk Train");
        $this->buildingBonus = [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE];
        $this->copies = 2;
    }
}
