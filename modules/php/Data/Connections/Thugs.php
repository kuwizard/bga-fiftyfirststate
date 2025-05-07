<?php

namespace Bga\Games\Fiftyfirststate\Data\Connections;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Connection;

class Thugs extends Connection
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CONNECTION_THUGS;
        $this->name = clienttranslate("Thugs");
        $this->action = new Act(
            [RESOURCE_GUN],
            [RESOURCE_ARROW_RED, RESOURCE_ARROW_RED, RESOURCE_ARROW_RED],
        );
        $this->copies = 2;
        $this->text[TEXT_DESCRIPTION] = clienttranslate('Spend 1 {gunIcon} to gain 3 {arrowRedIcon}');
    }
}
