<?php

namespace STATE\Data\Connections;

use STATE\Models\Act;
use STATE\Models\Connection;

class Merchants extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_MERCHANTS;
        $this->name = clienttranslate("Merchants");
        $this->action = new Act(
            [RESOURCE_WORKER, RESOURCE_WORKER],
            [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE],
        );
        $this->copies = 4;
    }
}
