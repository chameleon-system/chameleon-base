<?php

namespace ChameleonSystem\ExtranetBundle\LoginByTransferToken;

use esono\pkgCmsRouting\CollectionGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteGenerator implements CollectionGeneratorInterface
{
    public const ROUTE_NAME = 'chameleon_system_extranet.login_by_transfer_token';
    protected const CONTROLLER_SERVICE = 'chameleon_system_extranet.login_by_transfer_token.login_controller';
    protected const ACTION_REFERENCE = self::CONTROLLER_SERVICE.':loginAction';

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
