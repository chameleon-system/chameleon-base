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
use esono\pkgCmsCache\CacheInterface;
use TCMSRecord;

/**
 * @template T extends TCMSRecord
 *
 * @implements DataAccessInterface<T>
 */
readonly class CacheDataAccess implements DataAccessInterface
{
    public function __construct(
        private CacheInterface $cache,
        private LanguageServiceInterface $languageService,
        private DataAccessInterface $decorated)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll($languageId = null)
    {
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
        }
        $key = $this->cache->getKey([
            __CLASS__,
            'loadAll',
            get_class($this->decorated),
            $languageId,
        ]);
        /**
         * @var \TCMSRecord[] $elements
         *
         * @psalm-var T[] $elements
         */
        $elements = $this->cache->get($key);
        if (null === $elements) {
            $elements = $this->decorated->loadAll($languageId);
            $this->cache->set($key, $elements, $this->decorated->getCacheTriggers());
        }

        return $elements;
    }

    /**
     * @return string[]
     */
    public function getCacheTriggers()
    {
        return [];
    }
}
