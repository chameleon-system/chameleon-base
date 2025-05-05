<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\MapperLoader;

use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class MapperLoader implements MapperLoaderInterface
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapper($identifier)
    {
        try {
            $service = $this->serviceLocator->get($identifier);
        } catch (NotFoundExceptionInterface $exception) {
            if (false === \class_exists($identifier)) {
                throw new \LogicException(sprintf('Tried to instantiate mapper "%s", but neither a service with this ID nor a class with this name was found. Note it must be tagged with chameleon_system.mapper.', $identifier));
            }
            $service = new $identifier();
        }

        return $service;
    }
}
