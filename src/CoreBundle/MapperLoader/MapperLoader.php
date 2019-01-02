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

use IViewMapper;
use LogicException;
use Psr\Container\ContainerInterface;

class MapperLoader implements MapperLoaderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapper($identifier)
    {
        if (false === $this->container->has($identifier)) {
            if (false === class_exists($identifier)) {
                throw new LogicException(sprintf('Tried to instantiate mapper "%s", but neither a service with this ID and tagged with "chameleon_system.mapper" nor a class with this name was found.', $identifier));
            }

            return new $identifier();
        }

        $object = $this->container->get($identifier);

        if (false === $object instanceof IViewMapper) {
            throw new LogicException(sprintf('Tried to instantiate mapper "%s", but the resolved class does not implement IViewMapper.', $identifier));
        }

        return $object;
    }
}
