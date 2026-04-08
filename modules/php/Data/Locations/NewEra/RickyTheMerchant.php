<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\Production;

class RickyTheMerchant extends Production
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_RICKY_THE_MERCHANT;
        $this->name = clienttranslate("Ricky The Merchant");
        $this->distance = 1;
        $this->spoils = [RESOURCE_FUEL, RESOURCE_WORKER];
        $this->icons = [ICON_FUEL, ICON_ARROW];
        $this->deals = [RESOURCE_FUEL];
        $this->product = [RESOURCE_ARROW_BLUE];
        $this->copies = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => '1 {arrowBlueIcon}',
        ];
    }
}
