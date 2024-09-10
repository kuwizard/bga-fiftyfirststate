<?php

namespace STATE\Models;


use STATE\Managers\Resources;

class FeatureStorageMultiple extends FeatureStorage
{
    /**
     * @var ResourceStorageOption[]
     */
    protected array $resourcesOptions;

    public function addResource(int $resource)
    {
        $this->resources[] = $resource;
        Resources::add($this->id, $resource);
    }

    public function getResourcesOptionsNotFilled(): array
    {
        return array_values(array_filter($this->resourcesOptions, function ($option) {
            $optionResources = $option->getResources();
            $locationResources = $this->getResources();
            $locationResourcesFiltered = array_values(
                array_filter($locationResources, function ($resource) use ($optionResources) {
                    return in_array($resource, $optionResources);
                })
            );
            return count($locationResourcesFiltered) < $option->getLimit();
        }));
    }

    public function isFullyFilled(): bool
    {
        return empty($this->getResourcesOptionsNotFilled());
    }
}
