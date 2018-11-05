<?php

namespace ChameleonSystem\ViewRendererBundle\Bridge\Chameleon\RouteGenerator;

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;
use esono\pkgCmsRouting\CollectionGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class GenerateCssRouteCollectionGenerator implements CollectionGeneratorInterface
{
    /**
     * @var TPkgViewRendererLessCompiler
     */
    private $lessCompiler;

    public function __construct(TPkgViewRendererLessCompiler $lessCompiler)
    {
        $this->lessCompiler = $lessCompiler;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($config, \TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $path = $this->lessCompiler->getLocalPathToCompiledLess();
        $pattern = $this->lessCompiler->getCompiledCssFilenameRoutingPattern();

        $routeCollection = new RouteCollection();
        $routeCollection->add('chameleon_system_view_renderer.generate_css', new Route(
            $path.'/'.$pattern,
            [
                '_controller' => 'chameleon_system_view_renderer.controller.generate_css_controller',
                'containsPortalAndLanguagePrefix' => true,
            ],
            [
                'portalId' => '\d+',
            ]
        ));

        return $routeCollection;
    }
}
