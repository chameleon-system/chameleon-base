<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * through the config parameter "bShowLinkToParentRecord=true" you can activate a link
 * that can be used to jump to the parent record (assuming the user has the right permissions)
 * by setting bAllowEdit=true you can activate the right to select a different parent
 * note: all items will be made available.
 * /**/
class TCMSFieldLookupParentID extends TCMSFieldLookup implements DoctrineTransformableInterface
{
    private function isOneToOneConnection(): bool
    {
        $query = 'SELECT `only_one_record_tbl` from `cms_tbl_conf` WHERE `name` = :tableName';
        $onlyOneRecord = $this->getDatabaseConnection()->fetchOne(
            $query,
            ['tableName' => $this->sTableName]
        );
        if ('1' === $onlyOneRecord) {
            return true;
        }

        $query = 'SELECT `cms_field_conf`.`fieldtype_config`
                            FROM `cms_field_conf`
                            INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                            WHERE `cms_tbl_conf`.`name` = :tableName AND `cms_field_conf`.`name` = :fieldName';
        $fieldConfigRow = $this->getDatabaseConnection()->fetchAssociative(
            $query,
            ['tableName' => $this->GetConnectedTableName(), 'fieldName' => $this->getOwningFieldName()]
        );

        if (false === $fieldConfigRow) {
            return false;
        }

        $fieldConf = new TPkgCmsStringUtilities_ReadConfig($fieldConfigRow['fieldtype_config']);
        if ('true' === $fieldConf->getConfigValue('bOnlyOneRecord')) {
            return true;
        }

        return false;
    }

    protected function getDoctrineDataModelXml(string $namespace, array $tableNamespaceMapping): string
    {
        $propertyName = $this->name;
        if (str_ends_with($propertyName, '_id')) {
            $propertyName = substr($propertyName, 0, -3);
        }

        $parameters = [
            'fieldName' => $this->snakeToCamelCase($propertyName),
            'targetClass' => sprintf(
                '%s\\%s',
                $tableNamespaceMapping[$this->GetConnectedTableName()],
                $this->snakeToPascalCase($this->GetConnectedTableName())
            ),
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
        ];

        $hasOwner = null !== $this->getOwningFieldName();
        $viewName = 'mapping/many-to-one-owned.xml.twig';
        if (false === $hasOwner) {
            $viewName = 'mapping/many-to-one.xml.twig';
        } else {
            $parameters['owningCollectionProperty'] = $this->snakeToCamelCase($this->getOwningFieldName().'_collection');
            if (true === $this->isOneToOneConnection()) {
                $viewName = 'mapping/one-to-one-bidirectional-owned.xml.twig';
            }
        }

        return $this->getDoctrineRenderer($viewName, $parameters)->render();
    }

    public function GetHTML()
    {
        $sHTML = '';
        $bAllowEdit = $this->oDefinition->GetFieldtypeConfigKey('bAllowEdit');
        if ($bAllowEdit) {
            $sHTML = parent::GetHTML();
        } else {
            $sHTML = $this->getLinkToParentRecordIfSet();
        }

        return $sHTML;
    }

    private function getOwningFieldName(): ?string
    {
        $owningTable = $this->GetConnectedTableName();
        $owningTableConf = TdbCmsTblConf::GetNewInstance();
        $owningTableConf->LoadFromField('name', $owningTable);

        $fields = $owningTableConf->GetFields(new TCMSRecord());
        /* @var $field TCMSField */
        $fields->GoToStart();
        while ($field = $fields->Next()) {
            if (false === ($field instanceof TCMSFieldPropertyTable)) {
                continue;
            }
            if ($field->GetPropertyTableName() !== $this->sTableName) {
                continue;
            }

            if ($field->GetMatchingParentFieldName() !== $this->name) {
                continue;
            }

            return $field->name;
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getLinkToParentRecordIfSet()
    {
        $translator = $this->getTranslator();

        if (empty($this->data)) {
            return '<div class="form-content-simple">'.$translator->trans('chameleon_system_core.field_lookup.nothing_selected', [], 'admin').'</div>';
        }

        $html = $this->_GetHiddenField();

        $tblName = $this->GetConnectedTableName();
        $item = new TCMSRecord();
        $item->table = $tblName;
        if (false === $item->Load($this->data)) {
            $html .= '<div class="alert alert-warning">'.$translator->trans('chameleon_system_core.field_lookup.error_assigned_id_does_not_exists', ['%id%' => $this->data], 'admin').'</div>';

            return $html;
        }

        $showLinkToParentRecord = $this->oDefinition->GetFieldtypeConfigKey('bShowLinkToParentRecord');
        $itemName = $item->GetName();

        $foreignTableName = $this->GetConnectedTableName();
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if ('true' === $showLinkToParentRecord && '' !== $this->data && true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $foreignTableName)) {
            $html .= '<div class="d-flex align-items-center">';

            if ('' !== $itemName) {
                $html .= '<div class="mr-2">'.$itemName.'</div>';
            }
            $html .= '<div class="switchToRecordBox">'.TCMSRender::DrawButton(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup.switch_to'), $this->GetEditLinkForParentRecord(), 'fas fa-location-arrow').'</div>';
            $html .= '</div>';

            return $html;
        }

        $html .= '<div class="form-content-simple">'.$itemName.'</div>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        return $this->getLinkToParentRecordIfSet();
    }

    public function GetDisplayType()
    {
        $displayType = parent::GetDisplayType();

        $bAllowEdit = $this->oDefinition->GetFieldtypeConfigKey('bAllowEdit');
        if ('none' === $displayType && !$bAllowEdit) {
            $displayType = 'readonly';
        }

        return $displayType;
    }

    /**
     * return link to edit the parent record.
     *
     * @return string
     */
    protected function GetEditLinkForParentRecord()
    {
        $foreignTableName = $this->GetConnectedTableName();
        $oTableConf = TdbCmsTblConf::GetNewInstance();
        $oTableConf->LoadFromField('name', $foreignTableName);

        $sLinkParams = [
            'pagedef' => $this->getInputFilterUtil()->getFilteredGetInput('pagedef', 'tableeditor'),
            'tableid' => $oTableConf->id,
            'id' => urlencode($this->data),
        ];
        $sLink = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($sLinkParams);

        return $sLink;
    }

    /**
     * {@inheritDoc}
     */
    public function PostInsertHook($iRecordId)
    {
        parent::PostInsertHook($iRecordId);

        if (!empty($this->data)) {
            $this->getCacheService()->callTrigger($this->GetConnectedTableName(), $this->data);
        }
    }

    /**
     * called on each field when a record is saved.
     *
     * @param string $iRecordId
     */
    public function PostSaveHook($iRecordId)
    {
        parent::PostSaveHook($iRecordId);

        if (!empty($this->data)) {
            $this->getCacheService()->callTrigger($this->GetConnectedTableName(), $this->data);
        }
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
