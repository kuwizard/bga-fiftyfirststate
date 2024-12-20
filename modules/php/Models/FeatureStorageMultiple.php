<?php

namespace STATE\Models;


use STATE\Managers\Resources;

class FeatureStorageMultiple extends FeatureStorage
{
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

    public function isCanStoreResource(int $resource): bool
    {
        $availableOptions = $this->getResourcesOptionsNotFilled();
        $optionsWithGivenResource = array_filter($availableOptions, function ($option) use ($resource) {
            return in_array($resource, $option->getResources());
        });
        foreach ($this->resourcesOptions as $option) {
            if (in_array($resource, $option->getResources()) && !empty($optionsWithGivenResource)) {
                return true;
            }
        };
        return false;
    }
}
