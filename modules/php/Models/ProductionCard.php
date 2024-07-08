<?php

namespace STATE\Models;

class ProductionCard extends LocationCard
{
    /**
     * @var boolean
     */
    protected $isOpen;
    /**
     * @var int[]
     */
    protected $product;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->isOpen = false;
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'isOpen' => $this->isOpen,
        ]);
    }
}
