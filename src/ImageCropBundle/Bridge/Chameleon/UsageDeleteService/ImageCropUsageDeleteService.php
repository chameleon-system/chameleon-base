<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\UsageDeleteService;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use ChameleonSystem\MediaManager\Exception\UsageDeleteException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemUsageDeleteServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class ImageCropUsageDeleteService implements MediaItemUsageDeleteServiceInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var FieldTranslationUtil
     */
    private $fieldTranslationUtil;

    public function __construct(
        Connection $databaseConnection,
        LanguageServiceInterface $languageService,
        FieldTranslationUtil $fieldTranslationUtil
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->languageService = $languageService;
        $this->fieldTranslationUtil = $fieldTranslationUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUsage(MediaItemUsageDataModel $usage)
    {
        $fieldConf = $this->getFieldDefinitionFromUsage($usage);
        if (null === $fieldConf) {
            return false;
        }

        if ('CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP' !== $fieldConf->GetFieldType()->fieldConstname) {
            return false;
        }

        $defaultImages = explode(',', $fieldConf->sqlData['field_default_value']);
        $baseLanguageId = $this->languageService->getCmsBaseLanguageId();
        $success = true;
        $availableLanguageIds = $this->getAvailableLanguageIds();
        foreach ($availableLanguageIds as $languageId) {
            if (false === $fieldConf->fieldIsTranslatable && $languageId !== $baseLanguageId) {
                continue;
            }

            $language = $this->languageService->getLanguage($languageId);
            $fieldNameTranslated = $this->fieldTranslationUtil->getTranslatedFieldName(
                $usage->getTargetTableName(),
                $usage->getTargetFieldName(),
                $language
            );

            $query = sprintf(
                'SELECT %s AS fieldValue FROM %s WHERE `id` = :id',
                $this->databaseConnection->quoteIdentifier($fieldNameTranslated),
                $this->databaseConnection->quoteIdentifier($usage->getTargetTableName())
            );
            try {
                $row = $this->databaseConnection->fetchAssociative(
                    $query,
                    ['id' => $usage->getTargetRecordId()]
                );
            } catch (DBALException $e) {
                throw new UsageDeleteException(
                    sprintf(
                        'Could not delete usage in field %s.%s: %s',
                        $usage->getTargetFieldName(),
                        $usage->getTargetTableName(),
                        $e->getMessage()
                    ), 0, $e
                );
            }
            $images = explode(',', $row['fieldValue']);
            foreach ($images as $key => $imageId) {
                if ($imageId === $usage->getMediaItemId()) {
                    if (isset($defaultImages[$key])) {
                        $images[$key] = $defaultImages[$key];
                    } else {
                        $images[$key] = '0';
                    }
                }
            }
            $newValue = implode(',', $images);
            $tableEditor = new \TCMSTableEditorManager();
            $tableEditor->AllowEditByAll(true);
            $tableEditor->Init($fieldConf->fieldCmsTblConfId, $usage->getTargetRecordId(), $languageId);

            $success = $tableEditor->SaveField($usage->getTargetFieldName(), $newValue) && $success;
        }

        return $success;
    }

    /**
     * @return \TdbCmsFieldConf|null
     */
    private function getFieldDefinitionFromUsage(MediaItemUsageDataModel $usage)
    {
        $fieldConf = \TdbCmsFieldConf::GetNewInstance();
        if (false === $fieldConf->LoadFromFields(
            [
                'cms_tbl_conf_id' => $this->getTableId($usage->getTargetTableName()),
                'name' => $usage->getTargetFieldName(),
            ]
        )) {
            return null;
        }

        return $fieldConf;
    }

    /**
     * @param string $tableName
     *
     * @return string|null
     */
    private function getTableId($tableName)
    {
        try {
            return \TTools::GetCMSTableId($tableName);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * @return string[]
     */
    private function getAvailableLanguageIds()
    {
        $config = \TdbCmsConfig::GetNewInstance();
        $languageIds = [$config->GetFieldTranslationBaseLanguage()->id];
        $otherLanguages = $config->GetFieldBasedTranslationLanguageArray();

        return array_merge($languageIds, array_keys($otherLanguages));
    }
}
