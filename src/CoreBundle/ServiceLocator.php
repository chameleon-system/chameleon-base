<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * ServiceLocator provides static accessors for services and parameters from the Symfony container to provide Symfony
 * integration for legacy code. Prefer dependency injection over this locator wherever possible.
 */
class ServiceLocator
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @param string $serviceId
     *
     * @return object
     *
     * @throws ServiceCircularReferenceException if a circular reference is detected
     * @throws ServiceNotFoundException          if the service is not defined or the container isn't set yet
     *
     * @psalm-suppress InvalidNullableReturnType, NullableReturnStatement
     */
    public static function get($serviceId)
    {
        if (null === self::$container) {
            throw new ServiceNotFoundException('You requested the container in a state where it is not available. Use this method only after container initialization.');
        }

        return self::$container->get($serviceId);
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException          if the parameter is not defined
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException          if the container isn't set yet
     */
    public static function getParameter($name)
    {
        /*
         * Do not optimize the code duplication in this method. Introducing a shared method for container retrieval
         * would cost about 2% performance as get() and getParameter() are called hundreds of times per request.
         */
        if (null === self::$container) {
            throw new ServiceNotFoundException('You requested the container in a state where it is not available. Use this method only after container initialization.');
        }

        return self::$container->getParameter($name);
    }

    /**
     * @param ContainerInterface $container
     *
     * @internal
     *
     * @return void
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }
}
