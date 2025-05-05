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
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

abstract class ChameleonBaseRouter extends Router
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var UrlUtil
     */
    protected $urlUtil;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container, $resource, array $options = [], ?RequestContext $context = null)
    {
        parent::__construct($container, $resource, $options, $context);
        /*
         * @psalm-suppress InvalidPropertyAssignmentValue - We know that this is a string in this instance.
         */
        $this->environment = $container->getParameter('kernel.environment');

        $options['resource_type'] = 'chameleon';
        $options['cache_dir'] = $this->generateCacheDirPath((string) $container->getParameter('kernel.cache_dir'));

        $this->setOptions($options);
    }

    abstract protected function generateCacheDirPath(string $baseCacheDir): string;

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(): RouteCollection
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
        $currentDir = $this->generateCacheDirPath($this->getOption('cache_dir'));
        if (false === is_dir($currentDir)) {
            return;
        }
        $d = dir($currentDir);
        while (false !== ($entry = $d->read())) {
            $fullName = sprintf('%s%s%s', $currentDir, DIRECTORY_SEPARATOR, $entry);
            if ('.' === $entry || '..' === $entry || is_dir($fullName)) {
                continue;
            }
            unlink($fullName);
        }
        $d->close();
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
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
     * @param UrlUtil $urlUtil
     *
     * @return void
     */
    public function setUrlUtil($urlUtil)
    {
        $this->urlUtil = $urlUtil;
    }
}
