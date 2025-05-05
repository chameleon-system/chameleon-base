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

use esono\pkgCmsCache\CacheInterface;

class RoutingUtilCacheDecorator implements RoutingUtilInterface
{
    /**
     * @var RoutingUtilInterface
     */
    private $subject;
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(RoutingUtilInterface $routingUtil, CacheInterface $cache)
    {
        $this->subject = $routingUtil;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkForTreeNode(\TdbCmsTree $tree, \TdbCmsLanguage $language)
    {
        return $this->subject->getLinkForTreeNode($tree, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getHostRequirementPlaceholder()
    {
        return $this->subject->getHostRequirementPlaceholder();
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainRequirement(\TdbCmsPortal $portal, \TdbCmsLanguage $language, $secure)
    {
        return $this->subject->getDomainRequirement($portal, $language, $secure);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllPageRoutes(\TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $aKey = [
            'class' => __CLASS__,
            'function' => 'getAllPageRoutes',
            'portal' => $portal->id,
            'language' => $language->id,
        ];
        $key = $this->cache->getKey($aKey, false);
        $lookup = $this->cache->get($key);
        if (null === $lookup) {
            $lookup = $this->subject->getAllPageRoutes($portal, $language);

            $this->cache->set($key, $lookup, [
                ['table' => 'cms_tpl_page', 'id' => null],
                ['table' => 'cms_tree', 'id' => null],
                ['table' => 'cms_tree_node', 'id' => null],
                ['table' => 'cms_portal', 'id' => $portal->id],
            ]);
        }

        return $lookup;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeRoutePath($path, \TdbCmsPortal $portal)
    {
        return $this->subject->normalizeRoutePath($path, $portal);
    }
}
