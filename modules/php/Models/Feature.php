<?php

namespace STATE\Models;

class Feature extends Location
{
    /**
     * @var int
     */
    protected $featureType;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->featureType = FEATURE_NONE;
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
        ]);
    }
}
