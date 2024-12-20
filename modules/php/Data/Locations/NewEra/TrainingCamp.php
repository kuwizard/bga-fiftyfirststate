<?php

namespace STATE\Data\Locations\NewEra;

use STATE\Models\Act;
use STATE\Models\Action;

class TrainingCamp extends Action
{
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->type = CARD_TRAINING_CAMP;
        $this->name = clienttranslate("Training Camp");
        $this->distance = 2;
        $this->spoils = [RESOURCE_ARROW_RED, RESOURCE_WORKER, RESOURCE_CARD];
        $this->icons = [ICON_ARROW, ICON_GUN];
        $this->deals = [RESOURCE_ARROW_RED];
        $this->copies = 2;
        $this->activationsMax = 2;
        $this->action = new Act(
            [RESOURCE_WORKER],
            [RESOURCE_ARROW_RED],
        );
        $this->text = [
            ...$this->getText(),
            TEXT_DESCRIPTION => clienttranslate('Spend 1 {workerIcon} to gain 1 {arrowRedIcon}'),
        ];
    }
}
