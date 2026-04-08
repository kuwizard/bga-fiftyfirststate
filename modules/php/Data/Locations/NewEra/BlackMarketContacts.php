<?php

namespace Bga\Games\Fiftyfirststate\Data\Locations\NewEra;

use Bga\Games\Fiftyfirststate\Models\FeatureStorageMultiple;
use Bga\Games\Fiftyfirststate\Models\ResourceStorageOptionMulti;

class BlackMarketContacts extends FeatureStorageMultiple
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_BLACK_MARKET_CONTACTS;
        $this->name = clienttranslate("Black Market Contacts");
        $this->distance = 1;
        $this->spoils = [RESOURCE_ARROW_BLUE, RESOURCE_ARROW_BLUE];
        $this->icons = [ICON_ARROW, ICON_FUEL];
        $this->deals = [RESOURCE_ARROW_BLUE];
        $this->copies = 1;
        $this->buildingBonus = [RESOURCE_ARROW_BLUE];

        $this->resourcesOptions = [new ResourceStorageOptionMulti([RESOURCE_ARROW_BLUE], 3)];
        $this->text = [
            ...$this->getText(true),
            TEXT_DESCRIPTION => clienttranslate(
                'You may store up to 3 {arrowBlueIcon} during the Cleanup phase. Take them back during the next Production phase'
            ),
            TEXT_BONUS_DESCRIPTION => '1 {arrowBlueIcon}',
        ];
    }
}
