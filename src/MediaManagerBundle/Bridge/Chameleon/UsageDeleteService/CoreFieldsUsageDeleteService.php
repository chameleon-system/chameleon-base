<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\UsageDeleteService;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use ChameleonSystem\MediaManager\Exception\UsageDeleteException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemUsageDeleteServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class CoreFieldsUsageDeleteService implements MediaItemUsageDeleteServiceInterface
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
        try {
            $fieldConf = $this->getFieldDefinitionFromUsage($usage);
            if (null === $fieldConf) {
                return false;
            }

            $availableLanguageIds = $this->getAvailableLanguageIds();
            $constName = $fieldConf->GetFieldType()->fieldConstname;
            switch ($constName) {
                case 'CMSFIELD_TABLELIST':
                    return $this->deleteUsageFieldTableList($usage, $fieldConf, $availableLanguageIds);
                case 'CMSFIELD_MULTITABLELIST':
                    return $this->deleteUsageFieldMultiTableList($usage, $fieldConf);
                case 'CMSFIELD_MEDIA':
                case 'CMSFIELD_EXTENDEDTABLELIST_MEDIA':
                    return $this->deleteUsageFieldExtendedTableListMedia($usage, $fieldConf, $availableLanguageIds);
                case 'CMSFIELD_WYSIWYG':
                case 'CMSFIELD_WYSIWYG_LIGHT':
                    return $this->deleteUsageFieldWysiwyg($usage, $fieldConf, $availableLanguageIds);
                default:
                    return false;
            }
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
    }

    /**
     * @return \TdbCmsFieldConf|null
     */
    private function getFieldDefinitionFromUsage(MediaItemUsageDataModel $usage)
    {
        try {
            $fieldConf = \TdbCmsFieldConf::GetNewInstance();
            if (false === $fieldConf->LoadFromFields(
                [
                    'cms_tbl_conf_id' => $this->getTableId($usage->getTargetTableName()),
                    'name' => $usage->getTargetFieldName(),
                ]
            )
            ) {
                return null;
            }
        } catch (\InvalidArgumentException $e) {
            // table not found
            return null;
        }

        return $fieldConf;
    }

    /**
     * @param string $tableName
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getTableId($tableName)
    {
        return \TTools::GetCMSTableId($tableName);
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

    /**
     * @param string[] $availableLanguageIds
     *
     * @return bool
     */
    private function deleteUsageFieldTableList(
        MediaItemUsageDataModel $usage,
        \TdbCmsFieldConf $fieldConf,
        array $availableLanguageIds
    ) {
        $baseLanguageId = $this->languageService->getCmsBaseLanguageId();
        $success = false;
        foreach ($availableLanguageIds as $languageId) {
            if (false === $fieldConf->fieldIsTranslatable && $languageId !== $baseLanguageId) {
                continue;
            }
            $tableEditor = new \TCMSTableEditorManager();
            $tableEditor->AllowEditByAll(true);
            $tableEditor->Init($fieldConf->fieldCmsTblConfId, $usage->getTargetRecordId(), $languageId);

            $success = $tableEditor->SaveField(
                $usage->getTargetFieldName(),
                $fieldConf->sqlData['field_default_value']
            ) && $success;
        }

        return $success;
    }

    /**
     * @return bool
     */
    private function deleteUsageFieldMultiTableList(MediaItemUsageDataModel $usage, \TdbCmsFieldConf $fieldConf)
    {
        $tableEditor = new \TCMSTableEditorManager();
        $tableEditor->AllowEditByAll(true);
        $tableEditor->Init($fieldConf->fieldCmsTblConfId, $usage->getTargetRecordId());
        $tableEditor->RemoveMLTConnection($fieldConf->sqlData['name'], $usage->getMediaItemId());

        return true;
    }

    /**
     * @return bool
     *
     * @throws DBALException
     */
    private function deleteUsageFieldExtendedTableListMedia(
        MediaItemUsageDataModel $usage,
        \TdbCmsFieldConf $fieldConf,
        array $availableLanguageIds
    ) {
        $defaultImages = explode(',', $fieldConf->sqlData['field_default_value']);

        $baseLanguageId = $this->languageService->getCmsBaseLanguageId();
        $success = true;
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
            $row = $this->databaseConnection->fetchAssociative($query, ['id' => $usage->getTargetRecordId()]);
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
     * @return bool
     *
     * @throws DBALException
     */
    private function deleteUsageFieldWysiwyg(
        MediaItemUsageDataModel $usage,
        \TdbCmsFieldConf $fieldConf,
        array $availableLanguageIds
    ) {
        $matchString = "/<img([^>]+?)cmsmedia=['\"]".$usage->getMediaItemId()."['\"](.*?)\\/>/usi";

        $baseLanguageId = $this->languageService->getCmsBaseLanguageId();
        $success = true;
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
            $row = $this->databaseConnection->fetchAssociative(
                $query,
                ['id' => $usage->getTargetRecordId()]
            );
            $newValue = preg_replace($matchString, '', $row['fieldValue']);

            $tableEditor = new \TCMSTableEditorManager();
            $tableEditor->AllowEditByAll(true);
            $tableEditor->Init($fieldConf->fieldCmsTblConfId, $usage->getTargetRecordId(), $languageId);

            $success = $tableEditor->SaveField($usage->getTargetFieldName(), $newValue) && $success;
        }

        return $success;
    }
}
