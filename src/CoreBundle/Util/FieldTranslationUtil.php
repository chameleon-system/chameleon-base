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

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;

class FieldTranslationUtil
{
    public function __construct(
        private readonly RequestInfoServiceInterface $requestInfoService,
        private readonly LanguageServiceInterface $languageService,
        private readonly BackendSessionInterface $backendSession
    ) {
    }

    public function isTranslationNeeded(?\TdbCmsLanguage $language = null): bool
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

    public function getTranslatedFieldName(string $tableName, string $fieldName, ?\TdbCmsLanguage $language = null): string
    {
        $baseLanguage = $this->getBaseLanguage();

        if (null === $baseLanguage) {
            return $fieldName;
        }

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
        if (false === in_array($fieldName, $translatedFieldList)) {
            return $fieldName;
        }

        return $fieldName.'__'.$language->fieldIso6391;
    }

    private function getCurrentLanguage(): ?\TdbCmsLanguage
    {
        if ($this->requestInfoService->isBackendMode()) {
            $language = \TdbCmsLanguage::GetNewInstance($this->backendSession->getCurrentEditLanguageId());
        } else {
            $language = $this->languageService->getActiveLanguage();
        }

        return $language;
    }

    private function getBaseLanguage(): ?\TdbCmsLanguage
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
     */
    public function getTranslatedQuery(string $query): string
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
     * @param string[] $fieldList
     * @param \TdbCmsLanguage|null $language the language in which the translated fields should be expected. Defaults to
     *                                       the currently active language
     *
     * @return string[]
     */
    public function copyTranslationsToDefaultFields(array $fieldList, ?\TdbCmsLanguage $language = null)
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
     * @throws \InvalidArgumentException
     */
    public function makeFieldMultilingual(string $fieldId): void
    {
        $this->changeTranslatableState($fieldId, true);
    }

    /**
     * Marks a field as non-translatable and changes the database structure accordingly.
     *
     * @throws \InvalidArgumentException
     */
    public function makeFieldMonolingual(string $fieldId): void
    {
        $this->changeTranslatableState($fieldId, false);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function changeTranslatableState(string $fieldId, bool $translatable): void
    {
        $fieldConf = \TdbCmsFieldConf::GetNewInstance($fieldId);
        if (false === $fieldConf->sqlData) {
            throw new \InvalidArgumentException("Field with ID '$fieldId' not found.");
        }

        $translatableString = $translatable ? '1' : '0';
        $tableEditorManager = \TTools::GetTableEditorManager('cms_field_conf', $fieldId);
        $tableEditorManager->AllowEditByAll(true);
        $tableEditorManager->SaveField('is_translatable', $translatableString);
        $tableEditorManager->AllowEditByAll(false);
        $fieldConf->fieldIsTranslatable = $translatable;
        $fieldConf->sqlData['is_translatable'] = $translatableString;

        $fieldConf->UpdateFieldTranslationKeys();
    }
}
