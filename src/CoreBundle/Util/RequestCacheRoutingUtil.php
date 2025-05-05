<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

class RequestCacheRoutingUtil implements RoutingUtilInterface
{
    /**
     * @var RoutingUtilInterface
     */
    private $routingUtil;

    public function __construct(RoutingUtilInterface $routingUtil)
    {
        $this->routingUtil = $routingUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkForTreeNode(\TdbCmsTree $tree, ?\TdbCmsLanguage $language = null)
    {
        return $this->routingUtil->getLinkForTreeNode($tree, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getHostRequirementPlaceholder()
    {
        return $this->routingUtil->getHostRequirementPlaceholder();
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainRequirement(\TdbCmsPortal $portal, \TdbCmsLanguage $language, $secure)
    {
        $cacheKey = $this->getCacheKey([
            $portal->id,
            $language->id,
            $secure,
        ]);
        static $cache = [];
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        $value = $this->routingUtil->getDomainRequirement($portal, $language, $secure);
        $cache[$cacheKey] = $value;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllPageRoutes(\TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $cacheKey = $this->getCacheKey([
            $portal->id,
            $language->id,
        ]);
        static $cache = [];
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        $value = $this->routingUtil->getAllPageRoutes($portal, $language);
        $cache[$cacheKey] = $value;

        return $value;
    }

    /**
     * @return string
     */
    private function getCacheKey(array $params)
    {
        return md5(serialize($params));
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeRoutePath($path, \TdbCmsPortal $portal)
    {
        return $this->routingUtil->normalizeRoutePath($path, $portal);
    }
}
