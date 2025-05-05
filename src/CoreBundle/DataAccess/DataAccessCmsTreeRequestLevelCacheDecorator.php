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

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

class DataAccessCmsTreeRequestLevelCacheDecorator implements DataAccessCmsTreeInterface
{
    /**
     * @var DataAccessCmsTreeInterface
     */
    private $subject;

    /**
     * @var array
     */
    private $cache = [];
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    public function __construct(DataAccessCmsTreeInterface $subject, LanguageServiceInterface $languageService)
    {
        $this->subject = $subject;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll($languageId = null)
    {
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
        }
        $cacheKey = sprintf('all-%s', $languageId);

        if (false === array_key_exists($cacheKey, $this->cache)) {
            $this->cache[$cacheKey] = $this->subject->loadAll($languageId);
        }

        return $this->cache[$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllInvertedNoFollowRulePageIds()
    {
        $cacheKey = 'allInvertedNoFollowRulePageIds';

        if (false === array_key_exists($cacheKey, $this->cache)) {
            $this->cache[$cacheKey] = $this->subject->getAllInvertedNoFollowRulePageIds();
        }

        return $this->cache[$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getInvertedNoFollowRulePageIds($cmsTreeId)
    {
        $all = $this->getAllInvertedNoFollowRulePageIds();
        if (false === isset($all[$cmsTreeId])) {
            return [];
        }

        return $all[$cmsTreeId];
    }
}
