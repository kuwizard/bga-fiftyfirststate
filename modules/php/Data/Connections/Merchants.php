<?php

namespace Bga\Games\Fiftyfirststate\Data\Connections;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Connection;

class Merchants extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_MERCHANTS;
        $this->name = clienttranslate("Merchants");
        $this->action = new Act(
            [],
            [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE],
        );
        $this->copies = 4;
        $this->text[TEXT_DESCRIPTION] = clienttranslate('Gain 2 {arrowBlueIcon}');
    }
}
