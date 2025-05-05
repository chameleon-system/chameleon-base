<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\UsageFinder;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use Doctrine\DBAL\Connection;

class CoreImageFieldsUsageFinder extends AbstractImageFieldsUsageFinder
{
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var FieldTranslationUtil
     */
    private $fieldTranslationUtil;

    public function __construct(Connection $databaseConnection, UrlUtil $urlUtil, LanguageServiceInterface $languageService, FieldTranslationUtil $fieldTranslationUtil)
    {
        parent::__construct($databaseConnection, $urlUtil);
        $this->languageService = $languageService;
        $this->fieldTranslationUtil = $fieldTranslationUtil;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldTypeConstantNamesToHandle()
    {
        return [
            'CMSFIELD_TABLELIST',
            'CMSFIELD_MULTITABLELIST',
            'CMSFIELD_MEDIA',
            'CMSFIELD_EXTENDEDTABLELIST_MEDIA',
            'CMSFIELD_WYSIWYG',
            'CMSFIELD_WYSIWYG_LIGHT',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRecordsForField($fieldTypeConstantName, $fieldRow, MediaItemDataModel $mediaItem)
    {
        switch ($fieldTypeConstantName) {
            case 'CMSFIELD_TABLELIST':
                return $this->findRecordsForCmsFieldTableList(
                    $fieldRow['tableName'],
                    $fieldRow['fieldName'],
                    $mediaItem
                );
            case 'CMSFIELD_MULTITABLELIST':
                return $this->findRecordsForCmsFieldMultiTableList(
                    $fieldRow['tableName'],
                    $fieldRow['fieldName'],
                    $mediaItem
                );
            case 'CMSFIELD_MEDIA':
            case 'CMSFIELD_EXTENDEDTABLELIST_MEDIA':
                return $this->findRecordsForCmsFieldMedia(
                    $fieldRow['tableName'],
                    $fieldRow['fieldName'],
                    $mediaItem
                );
            case 'CMSFIELD_WYSIWYG':
            case 'CMSFIELD_WYSIWYG_LIGHT':
                return $this->findRecordsForCmsFieldWysiwyg(
                    $fieldRow['tableName'],
                    $fieldRow['fieldName'],
                    $mediaItem
                );
            default:
                throw new UsageFinderException(
                    sprintf(
                        'Received field constant %s that was not excpected and could not be handled.',
                        $fieldTypeConstantName
                    )
                );
        }
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     *
     * @return array
     */
    private function findRecordsForCmsFieldTableList($tableName, $fieldName, MediaItemDataModel $mediaItem)
    {
        if ('cms_media_mlt' !== $fieldName) {
            return [];
        }

        $query = sprintf(
            'SELECT * FROM %s WHERE %s = :mediaItemId',
            $this->databaseConnection->quoteIdentifier($tableName),
            $this->databaseConnection->quoteIdentifier($fieldName)
        );

        return $this->databaseConnection->fetchAllAssociative(
            $query,
            ['mediaItemId' => $mediaItem->getId()]
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     *
     * @return array
     */
    private function findRecordsForCmsFieldMultiTableList($tableName, $fieldName, MediaItemDataModel $mediaItem)
    {
        if ('cms_media_mlt' !== $fieldName) {
            return [];
        }

        $mltTableName = $tableName.'_'.$fieldName;
        $query = sprintf(
            'SELECT usageTarget.*
                     FROM %s AS usageTarget
                     LEFT JOIN %s AS mltTable ON usageTarget.`id` = mltTable.`source_id`
                     WHERE mltTable.`target_id` = :mediaItemId',
            $this->databaseConnection->quoteIdentifier($tableName),
            $this->databaseConnection->quoteIdentifier($mltTableName)
        );

        return $this->databaseConnection->fetchAllAssociative(
            $query,
            ['mediaItemId' => $mediaItem->getId()]
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     *
     * @return array
     */
    private function findRecordsForCmsFieldMedia($tableName, $fieldName, MediaItemDataModel $mediaItem)
    {
        $translatedFieldNames = $this->getTranslatedFieldNames($tableName, $fieldName);
        $wherePart = implode(' OR ', array_map(function ($element) {
            return sprintf('%1$s LIKE :mediaItemIdCommaFront OR
                %1$s LIKE :mediaItemIdCommaEnd OR
                %1$s LIKE :mediaItemIdCommaBoth OR
                %1$s = :mediaItemId', $this->databaseConnection->quoteIdentifier($element));
        }, $translatedFieldNames));

        $query = sprintf(
            'SELECT * FROM %s WHERE %s',
            $this->databaseConnection->quoteIdentifier($tableName),
            $wherePart
        );
        $mediaItemId = $mediaItem->getId();

        return $this->databaseConnection->fetchAllAssociative(
            $query,
            [
                'mediaItemIdCommaFront' => "%,$mediaItemId",
                'mediaItemIdCommaEnd' => "$mediaItemId,%'",
                'mediaItemIdCommaBoth' => "%,$mediaItemId,%",
                'mediaItemId' => $mediaItem->getId(),
            ]
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     *
     * @return array
     */
    private function findRecordsForCmsFieldWysiwyg($tableName, $fieldName, MediaItemDataModel $mediaItem)
    {
        $translatedFieldNames = $this->getTranslatedFieldNames($tableName, $fieldName);
        $wherePart = implode(' OR ', array_map(function ($element) {
            return sprintf('%s LIKE :mediaTag', $this->databaseConnection->quoteIdentifier($element));
        }, $translatedFieldNames));

        $query = sprintf(
            'SELECT * FROM %s WHERE %s',
            $this->databaseConnection->quoteIdentifier($tableName),
            $wherePart
        );

        return $this->databaseConnection->fetchAllAssociative(
            $query,
            ['mediaTag' => '%cmsmedia="'.$mediaItem->getId().'%']
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     *
     * @return string[]
     */
    private function getTranslatedFieldNames($tableName, $fieldName)
    {
        $config = \TdbCmsConfig::GetInstance();
        $languageIsoList = \array_keys($config->GetFieldBasedTranslationLanguageArray());
        $languages = [
            $config->GetFieldTranslationBaseLanguage(),
        ];
        foreach ($languageIsoList as $languageIso) {
            $languages[] = $this->languageService->getLanguageFromIsoCode($languageIso);
        }

        $fieldNames = [];
        foreach ($languages as $language) {
            $fieldNames[] = $this->fieldTranslationUtil->getTranslatedFieldName($tableName, $fieldName, $language);
        }
        $fieldNames = \array_unique($fieldNames);

        return $fieldNames;
    }
}
