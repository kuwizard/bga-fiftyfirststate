<?php

namespace STATE\Models;

class Act
{
    /**
     * @var int
     */
    protected $type;
    /**
     * @var int[]
     */
    protected $spendRequirements;
    /**
     * @var int[]
     */
    protected $bonus;

    public function __construct($spendRequirements, $bonus, $type = ACTION_TYPE_SPEND)
    {
        $this->spendRequirements = $spendRequirements;
        $this->bonus = $bonus;
        $this->type = $type;
    }

//    public function jsonSerialize()
//    {
//        return [
//            'id' => $this->id,
//        ];
//    }
}
