<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Production;

class HiddenForge extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HIDDEN_FORCE;
        $this->name = clienttranslate("Hidden Forge");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_RESPIRATOR, ICON_GUN];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->product = [RESOURCE_DEFENCE];
        $this->copies = 1;
        $this->defence = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {defenceIcon}'),
        ];
    }
}
