<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Field;

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;
use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropDataAccessInterface;
use ChameleonSystem\ImageCropBundle\Bridge\Chameleon\BackendModule\ImageCropEditorModule;
use Doctrine\DBAL\DBALException;

/**
 * {@inheritdoc}
 */
class TCMSFieldMediaWithImageCrop extends \TCMSFieldExtendedLookupMedia
{
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $lookupFieldDef = parent::getDoctrineDataModelParts($namespace, $tableNamespaceMapping);

        $propertyName = $this->getFieldNameOfAdditionalField($this->name);
        if (str_ends_with($propertyName, '_id')) {
            $propertyName = substr($propertyName, 0, -3);
        }

        $parameters = [
            'source' => __CLASS__,
            'type' => $this->snakeToPascalCase($this->GetConnectedTableName()),
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($propertyName),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/lookup.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/lookup.methods.php.twig', $parameters)->render();

        $cropImageDef = new DataModelParts(
            $propertyCode,
            $methodCode,
            '',
            [
                ltrim(
                    sprintf('%s\\%s', $tableNamespaceMapping[$this->GetConnectedTableName()], $this->snakeToPascalCase($this->GetConnectedTableName())),
                    '\\'
                ),
            ],
            true
        );

        return $lookupFieldDef->merge($cropImageDef);
    }

    protected function getDoctrineDataModelXml(string $namespace, array $tableNamespaceMapping): string
    {
        $lookupFieldMapping = parent::getDoctrineDataModelXml($namespace, $tableNamespaceMapping);

        $propertyName = $this->getFieldNameOfAdditionalField($this->name);
        if (str_ends_with($propertyName, '_id')) {
            $propertyName = substr($propertyName, 0, -3);
        }

        $viewName = 'mapping/many-to-one.xml.twig';

        $additionalFieldMapping = $this->getDoctrineRenderer($viewName, [
            'fieldName' => $this->snakeToCamelCase($propertyName),
            'targetClass' => sprintf(
                '%s\\%s',
                $tableNamespaceMapping[$this->GetConnectedTableName()],
                $this->snakeToPascalCase($this->GetConnectedTableName())
            ),
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
        ])->render();

        return $lookupFieldMapping."\n".$additionalFieldMapping;
    }

    /**
     * {@inheritDoc}
     */
    public function ChangeFieldDefinition($sOldName, $sNewName, $postData = null)
    {
        $originalDefinitionData = $this->oDefinition->sqlData;
        parent::ChangeFieldDefinition($sOldName, $sNewName, $postData);

        $additionalFieldNameOldFieldName = $this->getFieldNameOfAdditionalField($sOldName);
        $additionalFieldNameNewFieldName = $this->getFieldNameOfAdditionalField($sNewName);
        $databaseConnection = $this->getDatabaseConnection();

        $tableName = $databaseConnection->quoteIdentifier($this->sTableName);
        if (false !== $databaseConnection->fetchOne(sprintf('SHOW COLUMNS FROM %s LIKE :columnName', $tableName), ['columnName' => $additionalFieldNameOldFieldName])) {
            // For new fields, the additional field already has the right name, but we have no way to check that here so we just check if the column exists.
            $query = sprintf(
                'ALTER TABLE %s CHANGE %s %s CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL',
                $tableName,
                $databaseConnection->quoteIdentifier($additionalFieldNameOldFieldName),
                $databaseConnection->quoteIdentifier($additionalFieldNameNewFieldName)
            );
            $databaseConnection->executeQuery($query);

            $logChangeDataModels[] = new LogChangeDataModel($query);
            \TCMSLogChange::WriteTransaction($logChangeDataModels);
        }

        $translatedFieldDefinition = clone $this->oDefinition;
        $translatedFieldDefinition->sqlData['is_translatable'] = $postData['is_translatable'];
        $originalDefinitionData['name'] = $additionalFieldNameOldFieldName;
        $translatedFieldDefinition->sqlData['name'] = $additionalFieldNameNewFieldName;
        $translatedFieldDefinition->UpdateFieldTranslationKeys($originalDefinitionData);
    }

    /**
     * @param string $originalFieldName
     *
     * @return string
     */
    private function getFieldNameOfAdditionalField($originalFieldName)
    {
        return $originalFieldName.'_image_crop_id';
    }

    /**
     * {@inheritDoc}
     */
    public function PostSaveHook($iRecordId)
    {
        parent::PostSaveHook($iRecordId);
        $this->saveCropId($iRecordId);
    }

    /**
     * @param string $recordId
     *
     * @return void
     */
    private function saveCropId($recordId)
    {
        $cropId = '';
        $crop = \TdbCmsImageCrop::GetNewInstance();
        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
        $additionalFieldNameTranslated = $additionalFieldName;

        if (true === $this->oDefinition->isTranslatable()) {
            $editLanguage = \TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId());
            if (null !== $editLanguage && $this->getFieldTranslationUtil()->isTranslationNeeded($editLanguage)) {
                $additionalFieldNameTranslated .= '__'.$editLanguage->fieldIso6391;
            }
        }

        if (true === isset($this->oTableRow->sqlData[$additionalFieldName]) && $crop->Load(
            $this->oTableRow->sqlData[$additionalFieldName]
        )) {
            $cropId = $crop->id;
        }

        $databaseConnection = $this->getDatabaseConnection();
        $data = [$databaseConnection->quoteIdentifier($additionalFieldNameTranslated) => $cropId];
        $identifier = ['id' => $recordId];
        $databaseConnection->update($databaseConnection->quoteIdentifier($this->sTableName), $data, $identifier);

        $migrationQueryData = new MigrationQueryData(
            $this->sTableName,
            \TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId())->fieldIso6391
        );
        $migrationQueryData
            ->setFields($data)
            ->setWhereEquals($identifier);
        $queryData = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE)];
        \TCMSLogChange::WriteTransaction($queryData);
    }

    /**
     * {@inheritDoc}
     */
    public function PostInsertHook($iRecordId)
    {
        parent::PostInsertHook($iRecordId);
        $this->saveCropId($iRecordId);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function DeleteFieldDefinition()
    {
        parent::DeleteFieldDefinition();
        $this->dropAdditionalField();
    }

    /**
     * @return void
     */
    private function dropAdditionalField()
    {
        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);

        $databaseConnection = $this->getDatabaseConnection();
        $query = sprintf(
            'ALTER TABLE %s DROP %s',
            $databaseConnection->quoteIdentifier($this->sTableName),
            $databaseConnection->quoteIdentifier($additionalFieldName)
        );
        $databaseConnection->executeQuery($query);

        $logChangeDataModels[] = new LogChangeDataModel($query);
        \TCMSLogChange::WriteTransaction($logChangeDataModels);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function ChangeFieldTypePreHook()
    {
        $this->dropAdditionalField();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function ChangeFieldTypePostHook()
    {
        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);

        $databaseConnection = $this->getDatabaseConnection();
        $query = sprintf(
            'ALTER TABLE %s ADD %s CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL',
            $databaseConnection->quoteIdentifier($this->sTableName),
            $databaseConnection->quoteIdentifier($additionalFieldName)
        );

        $databaseConnection->executeQuery($query);

        $logChangeDataModels[] = new LogChangeDataModel($query);
        \TCMSLogChange::WriteTransaction($logChangeDataModels);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function RemoveFieldIndex()
    {
        try {
            parent::RemoveFieldIndex();

            $connection = $this->getDatabaseConnection();

            $quotedTableName = $connection->quoteIdentifier($this->sTableName);
            $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
            $quotedIndexName = $connection->quoteIdentifier($additionalFieldName);

            $query = 'ALTER TABLE '.$quotedTableName.' DROP INDEX '.$quotedIndexName;

            $connection->executeQuery($query);
            $transaction = [new LogChangeDataModel($query)];
            \TCMSLogChange::WriteTransaction($transaction);
        } catch (DBALException $e) {
            // dropping the index can fail when first creating the field or changing the field
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws DBALException
     */
    public function CreateFieldIndex($returnDDL = false)
    {
        parent::CreateFieldIndex($returnDDL);

        $connection = $this->getDatabaseConnection();

        $quotedTableName = $connection->quoteIdentifier($this->sTableName);
        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
        $quotedIndexName = $connection->quoteIdentifier($additionalFieldName);
        $query = "ALTER TABLE $quotedTableName ADD INDEX ( $quotedIndexName )";

        if ($returnDDL) {
            return $query.";\n";
        }

        $connection->executeQuery($query);
        $transaction = [new LogChangeDataModel($query)];
        \TCMSLogChange::WriteTransaction($transaction);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::getHtmlHeadIncludes();
        $includes[] = '<script type="text/javascript" src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemimagecrop/js/imageFieldWithCrop.js?v=1'
        ).'"></script>';
        $includes[] = '<link href="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemimagecrop/css/imageCropField.css'
        ).'" rel="stylesheet" />';

        return $includes;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function RenderFieldPropertyString()
    {
        $fieldPropertyString = parent::RenderFieldPropertyString();
        $viewParser = new \TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $viewData = $this->GetFieldWriterData();
        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
        $viewData['additionalFieldName'] = $additionalFieldName;
        $viewData['additionalFieldPropertyName'] = \TCMSTableToClass::PREFIX_PROPERTY.\TCMSTableToClass::ConvertToClassString(
            $additionalFieldName
        );
        $viewParser->AddVarArray($viewData);
        $fieldPropertyString .= $viewParser->RenderObjectView(
            'additionalProperties',
            'TCMSFields/TCMSFieldMediaWithImageCrop',
            'Customer'
        );

        return $fieldPropertyString;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function RenderFieldPostLoadString()
    {
        $viewParser = new \TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $viewData = $this->GetFieldWriterData();
        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
        $viewData['additionalFieldName'] = $additionalFieldName;
        $viewData['additionalFieldPropertyName'] = \TCMSTableToClass::PREFIX_PROPERTY.\TCMSTableToClass::ConvertToClassString(
            $additionalFieldName
        );
        $viewParser->AddVarArray($viewData);

        $postLoadString = $viewParser->RenderObjectView(
            'postload',
            'TCMSFields/TCMSFieldMediaWithImageCrop',
            'Customer'
        );

        return $postLoadString;
    }

    /**
     * {@inheritdoc}
     */
    protected function getHtmlRenderedHtml($bReadOnly)
    {
        $fieldHtml = '<div class="TCMSFieldMediaWithImageCrop">';
        $fieldHtml .= parent::getHtmlRenderedHtml($bReadOnly);

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('cropEditorUrl', $this->getImageCropEditorUrl());
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('imageIsSet', $this->isImageSet());
        $fieldHtml .= $viewRenderer->Render('imageCrop/TCmsFieldMediaWithImageCrop/button.html.twig');

        $imageId = $this->_GetFieldValue();
        $languageId = \TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId())->id;
        $crop = null;
        $cmsMedia = $this->getCmsMediaDataAccess()->getCmsMedia($imageId, $languageId);
        if (null !== $cmsMedia) {
            $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
            $crop = $this->getImageCropDataAccess()->getImageCropById(
                $this->oTableRow->sqlData[$additionalFieldName],
                $languageId
            );
        }

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('crop', $crop);
        $viewRenderer->AddSourceObject('imageId', $imageId);

        $parameters = [
            'pagedef' => ImageCropEditorModule::PAGEDEF_NAME,
            ImageCropEditorModule::URL_PARAM_IMAGE_ID => $imageId,
            '_pagedefType' => ImageCropEditorModule::PAGEDEF_TYPE,
            'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'],
            '_fnc' => 'getImageFieldInformation',
        ];
        $urlToGetImage = URL_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($parameters, '?', '&');
        $viewRenderer->AddSourceObject('urlToGetImage', $urlToGetImage);

        $fieldHtml .= $viewRenderer->Render('imageCrop/TCmsFieldMediaWithImageCrop/additionalFields.html.twig');

        $fieldHtml .= '</div>';

        return $fieldHtml;
    }

    /**
     * @return \ViewRenderer
     */
    protected function getViewRenderer()
    {
        $viewRenderer = parent::getViewRenderer();
        $viewRenderer->AddSourceObject('cmsMediaId', $this->_GetFieldValue());
        $viewRenderer->AddSourceObject('imageCropPresetSystemName', $this->getImageCropPresetSystemName());

        return $viewRenderer;
    }

    /**
     * @return string|null
     */
    private function getImageCropPresetSystemName()
    {
        return $this->getFieldTypeConfigKey('imageCropPresetSystemName');
    }

    /**
     * @return string
     */
    private function getImageCropEditorUrl()
    {
        $parameters = [
            'pagedef' => ImageCropEditorModule::PAGEDEF_NAME,
            '_pagedefType' => ImageCropEditorModule::PAGEDEF_TYPE,
        ];

        $additionalFieldName = $this->getFieldNameOfAdditionalField($this->name);
        if ('' !== $this->oTableRow->sqlData[$additionalFieldName]) {
            $preset = \TdbCmsImageCropPreset::GetNewInstance();
            if ($preset->Load($this->oTableRow->sqlData[$additionalFieldName])) {
                $parameters[ImageCropEditorModule::URL_PARAM_PRESET_NAME] = $preset->fieldSystemName;
            }
        } else {
            $presetSystemName = $this->getImageCropPresetSystemName();
            if (null !== $presetSystemName) {
                $parameters[ImageCropEditorModule::URL_PARAM_PRESET_NAME] = $presetSystemName;
            }
        }

        $restriction = $this->getImageCropPresetRestriction();
        if (0 !== count($restriction)) {
            $parameters[ImageCropEditorModule::URL_PARAM_PRESET_RESTRICTION] = implode(';', $restriction);
        }

        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        $isInModal = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        if (null !== $parentField && '' !== $parentField && '' === $isInModal) {
            $parentIFrame = $parentField.'_iframe';
            $parameters[ImageCropEditorModule::URL_PARAM_PARENT_IFRAME] = $parentIFrame;
        }

        return URL_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * @return string[]
     */
    private function getImageCropPresetRestriction()
    {
        $systemNames = $this->getFieldTypeConfigKey('imageCropPresetRestrictionSystemNames');
        if ('' === $systemNames) {
            return [];
        }
        $systemNames = explode(';', $systemNames);
        array_walk(
            $systemNames,
            function ($element) {
                return trim($element);
            }
        );

        return $systemNames;
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return bool
     */
    protected function isImageSet()
    {
        $fieldValue = $this->_GetFieldValue();

        return '' !== $fieldValue && (false === is_numeric($fieldValue) || $fieldValue >= 1000);
    }

    /**
     * @return CmsMediaDataAccessInterface
     */
    private function getCmsMediaDataAccess()
    {
        return ServiceLocator::get('chameleon_system_image_crop.cms_media_data_access');
    }

    /**
     * @return ImageCropDataAccessInterface
     */
    private function getImageCropDataAccess()
    {
        return ServiceLocator::get('chameleon_system_image_crop.image_crop_data_access');
    }

    /**
     * {@inheritdoc}
     */
    protected function getMediaFieldMappers($isReadOnly)
    {
        $mapper = parent::getMediaFieldMappers($isReadOnly);
        $mapper[] = 'chameleon_system_image_crop.mapper.media_field_image_box_with_crop';

        return $mapper;
    }

    private function getFieldTranslationUtil(): FieldTranslationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    protected function getBackendSession(): BackendSessionInterface
    {
        return ServiceLocator::get('chameleon_system_cms_backend.backend_session');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
