<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Production;

class HiddenForge extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_HIDDEN_FORGE;
        $this->name = clienttranslate("Hidden Forge");
        $this->distance = 1;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER];
        $this->icons = [ICON_RESPIRATOR, ICON_GUN];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->product = [RESOURCE_DEFENCE];
        $this->copies = 1;
        $this->isDefended = true;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('1 {defenceIcon}'),
        ];
    }

    public function jsonSerialize()
    {
        return [...parent::jsonSerialize(), 'isDefended' => false];
    }
}
