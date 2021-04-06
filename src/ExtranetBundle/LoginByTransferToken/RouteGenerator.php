<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\LoginByTransferToken;

use esono\pkgCmsRouting\CollectionGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteGenerator implements CollectionGeneratorInterface
{
    public const ROUTE_NAME = 'chameleon_system_extranet.login_by_transfer_token';
    private const CONTROLLER_SERVICE = 'chameleon_system_extranet.login_by_transfer_token.login_by_token_controller';
    private const ACTION_REFERENCE = self::CONTROLLER_SERVICE.':loginAction';

    /**
     * @param array $config
     *
     * @return RouteCollection
     */
    public function getCollection($config, \TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(self::ROUTE_NAME, new Route(
            '/_login_by_transfer_token_/{token}',
            ['_controller' => self::ACTION_REFERENCE],
        ));

        return $routeCollection;
    }
}
