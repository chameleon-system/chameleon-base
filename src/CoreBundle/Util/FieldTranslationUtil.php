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

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use InvalidArgumentException;
use TdbCmsFieldConf;
use TTools;

class FieldTranslationUtil
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @param RequestInfoServiceInterface $requestInfoService
     * @param LanguageServiceInterface    $languageService
     */
    public function __construct(
        RequestInfoServiceInterface $requestInfoService,
        LanguageServiceInterface $languageService
    ) {
        $this->requestInfoService = $requestInfoService;
        $this->languageService = $languageService;
    }

    /**
     * @param \TdbCmsLanguage|null $language
     *
     * @return bool
     */
    public function isTranslationNeeded(\TdbCmsLanguage $language = null)
    {
        if (null === $language) {
            $language = $this->getCurrentLanguage();
            if (null === $language) {
                return false; // it is possible that the language is not (yet) initialized when this method is called.
                // These calls cannot be translated, so we simply return false here.
            }
        }

        return $language->id !== $this->getBaseLanguage()->id;
    }

    /**
     * @param string               $tableName
     * @param string               $fieldName
     * @param \TdbCmsLanguage|null $language
     *
     * @return string
     */
    public function getTranslatedFieldName($tableName, $fieldName, \TdbCmsLanguage $language = null)
    {
        $baseLanguage = $this->getBaseLanguage();
        if (null === $language) {
            $language = $this->getCurrentLanguage();
            if (null === $language) {
                return $fieldName;
            }
        }
        if ($baseLanguage->id === $language->id) {
            return $fieldName;
        }
        $cmsConfig = \TdbCmsConfig::GetInstance();
        $translatableFieldList = $cmsConfig->GetListOfTranslatableFields();
        if (!isset($translatableFieldList[$tableName])) {
            return $fieldName;
        }
        $translatedFieldList = $translatableFieldList[$tableName];
        if (!in_array($fieldName, $translatedFieldList)) {
            return $fieldName;
        }

        return $fieldName.'__'.$language->fieldIso6391;
    }

    /**
     * @return \TdbCmsLanguage|null
     */
    private function getCurrentLanguage()
    {
        if ($this->requestInfoService->isBackendMode()) {
            $language = $this->languageService->getActiveEditLanguage();
        } else {
            $language = $this->languageService->getActiveLanguage();
        }

        return $language;
    }

    /**
     * @return \TdbCmsLanguage
     */
    private function getBaseLanguage()
    {
        static $baseLanguage = null;
        if (null === $baseLanguage) {
            $baseLanguage = $this->languageService->getCmsBaseLanguage();
        }

        return $baseLanguage;
    }

    /**
     * Translates all field names in the query where necessary. This requires that all table names and all field names
     * are enclosed in backticks.
     *
     * @param string $query
     *
     * @return string
     */
    public function getTranslatedQuery($query)
    {
        if (!$this->isTranslationNeeded()) {
            return $query;
        }
        $pattern = '#`([^`]+)`\.`([^`]+)`#';
        if (!preg_match_all($pattern, $query, $matches)) {
            return $query;
        }
        $completeMatches = $matches[0];
        $language = $this->getCurrentLanguage();
        foreach ($completeMatches as $index => $match) {
            $tableName = $matches[1][$index];
            $fieldName = $matches[2][$index];
            $translatedFieldName = $this->getTranslatedFieldName($tableName, $fieldName, $language);
            if ($fieldName !== $translatedFieldName) {
                $translatedPart = "`$tableName`.`$translatedFieldName`";
                $query = str_replace($match, $translatedPart, $query);
            }
        }

        return $query;
    }

    /**
     * Copies values from translated fields to the respective base field, e.g. the value of $fieldList['name__de'] will
     * be copied to $fieldList['name'] (in the return value - the passed $fieldList is not changed).
     * This is not a method to be proud of - it just helps managing translations in the backend list manager where
     * translations were not counted in originally.
     *
     * @param string[]             $fieldList
     * @param \TdbCmsLanguage|null $language  the language in which the translated fields should be expected. Defaults to
     *                                        the currently active language
     *
     * @return string[]
     */
    public function copyTranslationsToDefaultFields(array $fieldList, \TdbCmsLanguage $language = null)
    {
        if (null === $language) {
            $language = $this->getCurrentLanguage();
        }
        if (!$this->isTranslationNeeded($language)) {
            return $fieldList;
        }
        $languageSuffix = '__'.$language->fieldIso6391;
        foreach ($fieldList as $fieldName => $value) {
            $suffixedFieldName = $fieldName.$languageSuffix;
            if (isset($fieldList[$suffixedFieldName])) {
                $translatedValue = $fieldList[$suffixedFieldName];
                if ('' !== $translatedValue || false === CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
                    $fieldList[$fieldName] = $translatedValue;
                }
            }
        }

        return $fieldList;
    }

    /**
     * Marks a field as translatable and changes the database structure accordingly.
     *
     * @param string $fieldId
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function makeFieldMultilingual($fieldId)
    {
        $this->changeTranslatableState($fieldId, true);
    }

    /**
     * Marks a field as non-translatable and changes the database structure accordingly.
     *
     * @param string $fieldId
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function makeFieldMonolingual($fieldId)
    {
        $this->changeTranslatableState($fieldId, false);
    }

    /**
     * @param string $fieldId
     * @param bool   $translatable
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function changeTranslatableState($fieldId, $translatable)
    {
        $fieldConf = TdbCmsFieldConf::GetNewInstance($fieldId);
        if (false === $fieldConf->sqlData) {
            throw new InvalidArgumentException("Field with ID '$fieldId' not found.");
        }

        $translatableString = $translatable ? '1' : '0';
        $tableEditorManager = TTools::GetTableEditorManager('cms_field_conf', $fieldId);
        $tableEditorManager->AllowEditByAll(true);
        $tableEditorManager->SaveField('is_translatable', $translatableString);
        $tableEditorManager->AllowEditByAll(false);
        $fieldConf->fieldIsTranslatable = $translatable;
        $fieldConf->sqlData['is_translatable'] = $translatableString;

        $fieldConf->UpdateFieldTranslationKeys();
    }
}
