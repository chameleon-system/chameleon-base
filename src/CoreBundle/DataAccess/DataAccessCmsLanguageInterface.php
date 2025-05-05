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

interface DataAccessCmsLanguageInterface
{
    /**
     * Returns a language object. The object will be loaded using the $targetLanguageId for localization of the object
     * itself.
     * Returns null if the language could not be loaded.
     *
     * @param string $id
     * @param string $targetLanguageId
     *
     * @return \TdbCmsLanguage|null
     */
    public function getLanguage($id, $targetLanguageId);

    /**
     * Returns the raw language data as an array.
     * Returns null if the language data could not be loaded.
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getLanguageRaw($id);

    /**
     * Returns a language object for the passed ISO 639-1 code.
     * The object will be loaded using the $targetLanguageId for localization of the object itself.
     * Returns null if the language could not be loaded.
     *
     * @param string $isoCode
     * @param string $targetLanguageId
     *
     * @return \TdbCmsLanguage|null
     */
    public function getLanguageFromIsoCode($isoCode, $targetLanguageId);
}
