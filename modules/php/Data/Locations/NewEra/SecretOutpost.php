<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class SecretOutpost extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_SECRET_OUTPOST;
        $this->name = clienttranslate("Secret Outpost");
        $this->distance = 2;
        $this->spoils = [RESOURCE_WORKER, RESOURCE_WORKER, RESOURCE_GUN];
        $this->icons = [ICON_WORKER, ICON_GUN];
        $this->deals = [RESOURCE_WORKER];
        $this->copies = 1;
        $this->action = new Act(
            [RESOURCE_GUN],
            [RESOURCE_WORKER],
        );
        $this->activationsMax = 2;
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {gunIcon} to gain 1 {workerIcon}'),
        ];
    }
}
