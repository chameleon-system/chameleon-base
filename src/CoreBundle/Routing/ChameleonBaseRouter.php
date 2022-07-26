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

use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;

abstract class ChameleonBaseRouter extends Router
{
    /**
     * @var string
     */
    protected $environment;
    /**
     * @var \ICmsCoreRedirect
     */
    protected $redirect;
    /**
     * @var UrlUtil
     */
    protected $urlUtil;

    /**
     * {@inheritdoc}
     * @param mixed $resource
     */
    public function __construct(ContainerInterface $container, $resource, array $options = array(), RequestContext $context = null)
    {
        parent::__construct($container, $resource, $options, $context);
        /**
         * @psalm-suppress InvalidPropertyAssignmentValue - We know that this is a string in this instance.
         */
        $this->environment = $container->getParameter('kernel.environment');

        $options['matcher_cache_class'] = $this->getMatcherCacheClassName();
        $options['generator_cache_class'] = $this->getGeneratorCacheClassName();
        $options['resource_type'] = 'chameleon';
        $options['cache_dir'] = $container->getParameter('kernel.cache_dir');

        $this->setOptions($options);
    }

    /**
     * @return string
     */
    abstract protected function getMatcherCacheClassName();

    /**
     * @return string
     */
    abstract protected function getGeneratorCacheClassName();

    /**
     * @param string $newURL
     * @param bool $permanently
     *
     * @deprecated use chameleon_system_core.redirect::redirect() instead
     *
     * @return never
     */
    public function redirect($newURL, $permanently = false)
    {
        $this->redirect->redirect($newURL, $permanently ? Response::HTTP_MOVED_PERMANENTLY : Response::HTTP_FOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $this->resource = $this->getRouterConfig();
        }

        return parent::getRouteCollection();
    }

    /**
     * @return array
     */
    abstract protected function getRouterConfig();

    /**
     * @return void
     */
    public function clearCache()
    {
        $currentDir = $this->getOption('cache_dir');
        $matcherName = $currentDir.DIRECTORY_SEPARATOR.$this->getMatcherCacheClassName().'.php';
        @unlink($matcherName);
        @unlink($matcherName.'.meta');
        $generatorName = $currentDir.DIRECTORY_SEPARATOR.$this->getGeneratorCacheClassName().'.php';
        @unlink($generatorName);
        @unlink($generatorName.'.meta');
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        /*
         * Remove an existing authenticity token (might be set to a concrete value instead of the placeholder)
         */
        $this->urlUtil->removeAuthenticityTokenFromArray($parameters);
        $url = parent::generate($name, $parameters, $referenceType);
        /*
         * Add authenticity token without URL encoding
         */
        $this->urlUtil->addAuthenticityTokenToUrlStringIfRequired($url, $parameters, '&');

        return $url;
    }

    /**
     * @param \ICmsCoreRedirect $redirect
     *
     * @return void
     */
    public function setRedirect(\ICmsCoreRedirect $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @param UrlUtil $urlUtil
     *
     * @return void
     */
    public function setUrlUtil($urlUtil)
    {
        $this->urlUtil = $urlUtil;
    }
}
