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
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class MapperLoader implements MapperLoaderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapper($identifier)
    {
        try {
            $service = $this->container->get($identifier);
        } catch (NotFoundExceptionInterface $exception) {
            if (false === \class_exists($identifier)) {
                throw new LogicException(sprintf('Tried to instantiate mapper "%s", but neither a service with this ID nor a class with this name was found.', $identifier));
            }
            $service = new $identifier();
        }

        return $service;
    }
}
