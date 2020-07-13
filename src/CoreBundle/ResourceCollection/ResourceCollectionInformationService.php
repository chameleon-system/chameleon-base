<?php

namespace ChameleonSystem\CoreBundle\ResourceCollection;

use ChameleonSystem\CoreBundle\Interfaces\ResourceCollectorInterface;

class ResourceCollectionInformationService implements ResourceCollectionInformationServiceInterface
{
    /**
     * @var ResourceCollectorInterface
     */
    private $resourceCollector;

    public function __construct(ResourceCollectorInterface $resourceCollector)
    {
        $this->resourceCollector = $resourceCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(): bool
    {
        return $this->resourceCollector->IsAllowed();
    }
}
