<?php

namespace STATE\Data\Connections;

use STATE\Models\Act;
use STATE\Models\Connection;

class Punks extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_PUNKS;
        $this->name = clienttranslate("Punks");
        $this->action = new Act(
            [],
            [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED],
        );
        $this->copies = 4;
    }
}
