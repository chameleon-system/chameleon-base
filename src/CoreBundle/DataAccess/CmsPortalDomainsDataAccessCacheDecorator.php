<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CmsPortalDomainsDataAccessCacheDecorator implements CmsPortalDomainsDataAccessInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $subject;

    /**
     * @param ContainerInterface                  $container
     * @param CmsPortalDomainsDataAccessInterface $subject
     */
    public function __construct(ContainerInterface $container, CmsPortalDomainsDataAccessInterface $subject)
    {
        $this->container = $container; // Avoid circular dependency on CacheInterface.
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryDomain($portalId, $languageId)
    {
        $cache = $this->getCache();
        $cacheKey = $cache->getKey(array(
            get_class(),
            'getPrimaryDomain',
            get_class($this->subject),
            $portalId,
            $languageId,
        ));
        $primaryDomain = $cache->get($cacheKey);
        if (null !== $primaryDomain) {
            return $primaryDomain;
        }

        $primaryDomain = $this->subject->getPrimaryDomain($portalId, $languageId);
        $cache->set($cacheKey, $primaryDomain, array(
            array('table' => 'cms_portal_domains', 'id' => null),
        ));

        return $primaryDomain;
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return $this->container->get('chameleon_system_cms_cache.cache');
    }
}
