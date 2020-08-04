<?php

namespace ChameleonSystem\CoreBundle\ResourceCollection;

interface ResourceCollectionInformationServiceInterface
{
    /**
     * Returns true if the resource collection is enabled and not locked or otherwise
     * blocked for the current request.
     */
    public function isActive(): bool;
}
