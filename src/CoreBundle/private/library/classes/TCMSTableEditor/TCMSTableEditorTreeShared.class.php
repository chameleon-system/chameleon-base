<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

class TCMSTableEditorTreeShared extends TCMSTableEditor
{
    /**
     * switch to prevent copy, new and delete buttons.
     *
     * @var bool
     */
    protected $editOnly = true;

    /**
     * called after inserting a new record.
     *
     * @param TIterator $oFields - the fields inserted
     */
    protected function PostInsertHook($oFields)
    {
        // get the parent_id from oFields
        $parentId = 0;
        $oFields->GoToStart();
        $bFoundField = false;
        while (!$bFoundField && ($oField = $oFields->Next())) {
            /** @var $oField TCMSField */
            if ('parent_id' == $oField->name) {
                $parentId = $oField->data;
                $bFoundField = true;
            }
        }
        $oFields->GoToStart();
        $countQuery = 'SELECT MAX(`entry_sort`) AS newsort
                      FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`
                     WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($parentId)."'
                       AND `id` <> '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                     ";
        if ($counttemp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($countQuery))) {
            $entry_sort = $counttemp['newsort'] + 1;
            $this->SaveField('entry_sort', $entry_sort);
        }
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator $oFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);
        // update cache
        $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $this->sId);
    }

    /**
     * returns an iterator with the menuitems for the current table. if you want to add your own
     * items, overwrite the GetCustomMenuItems (NOT GetMenuItems)
     * the iterator will always be reset to start.
     *
     * @return TIterator
     */
    public function GetMenuItems()
    {
        if (is_null($this->oMenuItems)) {
            $this->oMenuItems = new TIterator();
            // std menuitems...
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

            $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
            if ($tableInUserGroup) {
                // edit
                if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.save');
                    $oMenuItem->sItemKey = 'save';
                    $oMenuItem->sIcon = 'far fa-save';
                    $oMenuItem->sOnClick = 'SaveTreeNodeAjax();';
                    $this->oMenuItems->AddItem($oMenuItem);
                }
                // now add custom items
                $this->GetCustomMenuItems();
            }
        } else {
            $this->oMenuItems->GoToStart();
        }

        return $this->oMenuItems;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = '<style type="text/css">
      .tableeditcontainer {
        margin: 5px 0px 0px 0px;
      }

      .tableeditcontainer .leftTD {
        max-width: 150px;
      }
      </style>';

        $aIncludes[] = "<script type=\"text/javascript\">
      function SaveTreeNodeAjax() {
        document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ExecuteAjaxCall';
        document.cmseditform._fnc.value = 'AjaxSave';

        PostAjaxForm('cmseditform', function(data,statusText) {
          if(SaveViaAjaxCallback(data,statusText)) {
            parent.CreateTreeNode(data);
          }
        });
      }
      </script>";

        return $aIncludes;
    }

    /**
     * allows subclasses to overwrite default values.
     *
     * @param TIterator $oFields
     */
    public function _OverwriteDefaults($oFields)
    {
        parent::_OverwriteDefaults($oFields);
        $oGlobal = TGlobal::instance();

        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            /** @var $oField TCMSField */
            if ('parent_id' == $oField->name) {
                $oField->data = $oGlobal->GetUserData('parent_id');
            }
        }
        $oFields->GoToStart();
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        if (null !== $sId) {
            // delete children
            $sTreeTableName = $this->oTable->table;
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTreeTableName)."` WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
            $rChildren = MySqlLegacySupport::getInstance()->query($query);
            while ($aChild = MySqlLegacySupport::getInstance()->fetch_assoc($rChildren)) {
                $oTreeEditor = new TCMSTableEditorManager();
                $oTreeEditor->Init($this->oTableConf->id, $aChild['id']);
                $oTreeEditor->Delete($aChild['id']);
                unset($oTreeEditor);
            }
        }

        parent::Delete($sId);
    }

    /**
     * fetches short record data for processing after an ajaxSave
     * is returned by Save method
     * id and name is always available in the returned object
     * overwrite this method to add custom return data.
     *
     * @param array $postData
     *
     * @return object TCMSstdClass
     */
    public function GetObjectShortInfo($postData = [])
    {
        $oRecordData = parent::GetObjectShortInfo($postData);
        $oRecordData->parentID = $this->oTable->sqlData['parent_id'];

        return $oRecordData;
    }
}
