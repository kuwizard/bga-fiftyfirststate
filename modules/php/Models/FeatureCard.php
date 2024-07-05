<?php

namespace STATE\Models;

class FeatureCard extends LocationCard
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
            'isOpen' => $this->isOpen,
        ]);
    }
}
