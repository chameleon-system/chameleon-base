<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class TPkgDependencyInjection provides a static accessor for the service container to provide Symfony integration
 * for legacy code.
 * Use dependency injection wherever possible - this class is only for cases where real dependency injection is not
 * possible or causes major pain.
 *
 * @deprecated since 6.2.0 - use ChameleonSystem\CoreBundle\ServiceLocator instead.
 */
class TPkgDependencyInjection
{
    /**
     * @throws ServiceNotFoundException if the container isn't set yet
     *
     * @return ContainerInterface
     */
    public static function constructContainer()
    {
        return ServiceLocator::get('service_container');
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        ServiceLocator::setContainer($container);
    }

    public static function clearContainer()
    {
    }
}
