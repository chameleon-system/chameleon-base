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

class DataAccessCmsLanguageRequestLevelCacheDecorator implements DataAccessCmsLanguageInterface
{
    private array $cache = [];

    public function __construct(private readonly DataAccessCmsLanguageInterface $subject)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage($id, $targetLanguageId)
    {
        $key = 'getLanguage-'.$id;
        if (false === isset($this->cache[$key])) {
            $languageRaw = $this->getLanguageRaw($id);
            if (null === $languageRaw) {
                return null;
            }
            $language = \TdbCmsLanguage::GetNewInstance();
            $language->SetLanguage($targetLanguageId);
            $language->LoadFromRow($languageRaw);
            $this->cache[$key] = $language;
        }
        $language = $this->cache[$key];
        if ($targetLanguageId !== $language->GetLanguage()) {
            $language->SetLanguage($targetLanguageId);
            $language->LoadFromRow($language->sqlData);
        }

        return $language;
    }

    /**
     *{@inheritdoc}
     */
    public function getLanguageRaw($id)
    {
        $key = 'getLanguageRaw-'.$id;
        if (false === isset($this->cache[$key])) {
            $this->cache[$key] = $this->subject->getLanguageRaw($id);
        }

        return $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageFromIsoCode($isoCode, $targetLanguageId)
    {
        $key = 'getLanguageFromIsoCode-'.$isoCode;
        if (false === isset($this->cache[$key])) {
            $language = $this->subject->getLanguageFromIsoCode($isoCode, $targetLanguageId);
            if (null === $language) {
                return null;
            }
            $this->cache[$key] = $language;
        }
        /**
         * @var \TdbCmsLanguage $language
         */
        $language = $this->cache[$key];
        if ($targetLanguageId !== $language->GetLanguage()) {
            $language->SetLanguage($targetLanguageId);
            $language->LoadFromRow($language->sqlData);
        }

        return $language;
    }
}
