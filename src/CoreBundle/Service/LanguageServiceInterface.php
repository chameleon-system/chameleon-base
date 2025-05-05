<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

/**
 * LanguageServiceInterface defines a service that sets and returns the currently active language.
 */
interface LanguageServiceInterface
{
    /**
     * Returns the language ID defined in the backend as the base language.
     * This is the language used for all multi-language fields if you do not specify the locale as part of the field name.
     *
     * @return string
     */
    public function getCmsBaseLanguageId();

    /**
     * Returns the language defined in the backend as the base language.
     * This is the language used for all multi-language fields if you do not specify the locale as part of the field name.
     *
     * @return \TdbCmsLanguage|null
     */
    public function getCmsBaseLanguage();

    /**
     * Returns the ISO 639-1 language code of the language with the passed ID, or null if the language could not be loaded.
     * If null is passed for $languageId, the active language will be used.
     *
     * @param string $languageId
     *
     * @return string|null
     */
    public function getLanguageIsoCode($languageId = null);

    /**
     * Returns a language object for the passed ISO 639-1 code.
     * The object will be loaded using the $targetLanguageId for localization of the object itself.
     * If null is passed for $targetLanguageId, the active language will be used.
     * Returns null if the language could not be loaded.
     *
     * @param string $isoCode
     * @param string|null $targetLanguageId
     *
     * @return \TdbCmsLanguage|null
     */
    public function getLanguageFromIsoCode($isoCode, $targetLanguageId = null);

    /**
     * @see GetActiveLanguage()
     *
     * @return string|null
     */
    public function getActiveLanguageId();

    /**
     * Returns a language object with the passed ID, or null if the language could not be loaded.
     * The object will be loaded using the $targetLanguageId for localization of the object itself.
     * If null is passed for $targetLanguageId, the active language will be used.
     *
     * @param string $id
     * @param string|null $targetLanguageId
     *
     * @return \TdbCmsLanguage|null
     */
    public function getLanguage($id, $targetLanguageId = null);

    /**
     * Returns the currently active language, determined by the request (domain, language prefix, active page). If no
     * request is available or the language could not be determined for other reasons (mostly because we're in the backend
     * without a valid user, e.g. for cron jobs), we return the system's base language.
     *
     * @return \TdbCmsLanguage|null
     */
    public function getActiveLanguage();

    /**
     * @param string $languageId
     *
     * @return void
     */
    public function setActiveLanguage($languageId);

    /**
     * Returns the currently active locale (ISO 6391 code of the currently active language),
     * or null if there is no active language.
     *
     * @return string|null
     */
    public function getActiveLocale();

    /**
     * @return void
     */
    public function setFallbackLanguage(\TdbCmsLanguage $fallbackLanguage);
}
