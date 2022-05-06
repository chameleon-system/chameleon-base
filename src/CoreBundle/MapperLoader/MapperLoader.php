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

use LogicException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class MapperLoader implements MapperLoaderInterface
{
    private ServiceLocator $serviceLocator;
    private LoggerInterface $logger;

    public function __construct(ServiceLocator $serviceLocator, LoggerInterface $logger)
    {
        $this->serviceLocator = $serviceLocator;
        $this->logger = $logger;
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
                $errorMessage = sprintf('Tried to instantiate mapper "%s", but neither a service with this ID nor a class with this name was found. Note it must be tagged with chameleon_system.mapper.', $identifier);
                $this->logger->critical($errorMessage, ['exception' => $exception]);

                if (_DEVELOPMENT_MODE) {
                    throw new LogicException($errorMessage, $exception->getCode(), $exception);
                }

                return new \NullMapper();
            }
            $service = new $identifier();
        }

        return $service;
    }
}
