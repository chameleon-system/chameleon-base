<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Routing;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

interface PortalAndLanguageAwareRouterInterface extends RouterInterface
{
    /**
     * @param string $name
     * @param array $parameters Additional parameters for the route. There is a special parameter 'domain' which specifies
     *                          the host part for the generated URL (the name of the parameter may vary - always get
     *                          it via RoutingUtilInterface::getHostRequirementPlaceholder()).
     * @param int $referenceType
     *
     * @psalm-param UrlGeneratorInterface::* $referenceType
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function generateWithPrefixes($name, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH);
}
