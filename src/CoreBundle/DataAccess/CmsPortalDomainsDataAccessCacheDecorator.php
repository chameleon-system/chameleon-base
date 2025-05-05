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
        $cacheKey = $cache->getKey([
            get_class(),
            'getPrimaryDomain',
            get_class($this->subject),
            $portalId,
            $languageId,
        ]);
        $primaryDomain = $cache->get($cacheKey);
        if (null !== $primaryDomain) {
            return $primaryDomain;
        }

        $primaryDomain = $this->subject->getPrimaryDomain($portalId, $languageId);
        $cache->set($cacheKey, $primaryDomain, [
            ['table' => 'cms_portal_domains', 'id' => null],
        ]);

        return $primaryDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function getPortalPrefixListForDomain(string $domainName): array
    {
        $cache = $this->getCache();
        $cacheKey = $cache->getKey([
            __METHOD__,
            \get_class($this->subject),
            $domainName,
        ]);
        $value = $cache->get($cacheKey);
        if (null !== $value) {
            return $value;
        }

        $value = $this->subject->getPortalPrefixListForDomain($domainName);
        $cache->set($cacheKey, $value, [
            ['table' => 'cms_portal', 'id' => null],
            ['table' => 'cms_portal_domains', 'id' => null],
        ]);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePortalCandidate(array $idRestrictionList, string $identifierRestriction, bool $allowInactivePortals): ?array
    {
        return $this->subject->getActivePortalCandidate($idRestrictionList, $identifierRestriction, $allowInactivePortals);
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainDataByName(string $domainName): array
    {
        $cache = $this->getCache();
        $cacheKey = $cache->getKey([
            __METHOD__,
            \get_class($this->subject),
            $domainName,
        ]);
        $value = $cache->get($cacheKey);
        if (null !== $value) {
            return $value;
        }

        $value = $this->subject->getDomainDataByName($domainName);
        $cache->set($cacheKey, $value, [
            ['table' => 'cms_portal_domains', 'id' => null],
        ]);

        return $value;
    }

    private function getCache(): CacheInterface
    {
        return $this->container->get('chameleon_system_cms_cache.cache');
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDomainNames(): array
    {
        return $this->subject->getAllDomainNames();
    }
}
