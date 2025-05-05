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
use TCMSRecord;

/**
 * @template T extends TCMSRecord
 *
 * @implements DataAccessInterface<T>
 */
class RequestCacheDataAccess implements DataAccessInterface
{
    /**
     * @var array<string, T[]>
     */
    private $cache = [];
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var DataAccessInterface
     */
    private $decorated;

    /**
     * @param DataAccessInterface<T> $decorated
     */
    public function __construct(LanguageServiceInterface $languageService, DataAccessInterface $decorated)
    {
        $this->languageService = $languageService;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll($languageId = null)
    {
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
        }
        if (null === $languageId) {
            $languageId = 'no-language';
        }
        if (!isset($this->cache[$languageId])) {
            $this->cache[$languageId] = $this->decorated->loadAll($languageId);
        }
        $elements = $this->cache[$languageId]; // reference assignment ok
        if (!empty($elements)) {
            reset($elements);
            if ('no-language' !== $languageId && $languageId !== current($elements)->GetLanguage()) {
                foreach ($elements as $element) {
                    $element->SetLanguage($languageId);
                    $element->LoadFromRow($element->sqlData);
                }
                unset($element);
                reset($elements);
            }
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
