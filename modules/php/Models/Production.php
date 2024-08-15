<?php

namespace STATE\Models;

class Production extends Location
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

    public function getProduct()
    {
        return $this->product;
    }

    public function getFactionRow()
    {
        return 'production';
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'isOpen' => $this->isOpen,
        ]);
    }
}
