<?php

namespace STATE\Data\Connections;

use STATE\Models\Act;
use STATE\Models\Connection;

class JunkTrain extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_JUNK_TRAIN;
        $this->name = clienttranslate("Junk Train");
        $this->action = new Act(
            [],
            [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE],
        );
        $this->copies = 2;
        $this->text[TEXT_DESCRIPTION] = clienttranslate('Gain 3 {arrowBlueIcon}');
    }
}
