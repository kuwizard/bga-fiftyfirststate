<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Production;

class LemmysStorage extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_LEMMYS_STORAGE;
        $this->name = clienttranslate("Lemmy's Storage");
        $this->distance = 2;
        $this->spoils = [RESOURCE_AMMO, RESOURCE_AMMO, RESOURCE_VP];
        $this->icons = [ICON_AMMO];
        $this->deals = [RESOURCE_AMMO];
        $this->product = [RESOURCE_AMMO];
        $this->copies = 1;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {ammoIcon}',
        ];
    }
}
