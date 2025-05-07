<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Act;
use Bga\Games\Fiftyfirststate\Models\Action;

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
