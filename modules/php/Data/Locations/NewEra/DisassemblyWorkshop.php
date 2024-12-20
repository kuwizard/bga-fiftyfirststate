<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class DisassemblyWorkshop extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_DISASSEMBLY_WORKSHOP;
        $this->name = clienttranslate("Disassembly Workshop");
        $this->distance = 1;
        $this->spoils = [RESOURCE_IRON, RESOURCE_IRON];
        $this->icons = [ICON_IRON];
        $this->deals = [RESOURCE_IRON];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_IRON, RESOURCE_IRON],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 2 {ironIcon}'),
        ];
    }
}
