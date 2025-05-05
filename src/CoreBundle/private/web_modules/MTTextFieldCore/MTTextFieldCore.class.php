<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

class MTTextFieldCore extends TUserCustomModelBase
{
    /**
     * holds the record from the module_text.
     *
     * @var TdbModuleText
     */
    protected $_oTableRow;

    protected $bAllowHTMLDivWrapping = true;

    public function Execute()
    {
        $this->data = parent::Execute();
        $this->LoadTableRow();
        $this->data['oTableRow'] = $this->_oTableRow;

        return $this->data;
    }

    protected function LoadTableRow()
    {
        $oTdbModuleText = TdbModuleText::GetNewInstance();
        /* @var $oTdbModuleText TdbModuleText */
        $oTdbModuleText->SetLanguage($this->getLanguageService()->getActiveLanguageId());
        if ($oTdbModuleText->LoadFromField('cms_tpl_module_instance_id', $this->instanceID)) {
            $this->_oTableRow = $oTdbModuleText;
        }
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * if the content that is to be cached comes from the database (as it is most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        $tableInfo = parent::_GetCacheTableInfos();
        if (!is_array($tableInfo)) {
            $tableInfo = [];
        }
        if (is_object($this->_oTableRow)) {
            $tableInfo[] = ['table' => 'module_text', 'id' => $this->_oTableRow->id];
        }
        $tableInfo[] = ['table' => 'cms_media', 'id' => null];
        $tableInfo[] = ['table' => 'cms_document', 'id' => null];
        $tableInfo[] = ['table' => 'cms_media_tree', 'id' => null];
        $tableInfo[] = ['table' => 'cms_document_tree', 'id' => null];
        $tableInfo[] = ['table' => 'module_gallery', 'id' => null];
        $tableInfo[] = ['table' => 'cms_tree', 'id' => null];
        $tableInfo[] = ['table' => 'cms_tree_node', 'id' => null];

        return $tableInfo;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('common/textBlock'));

        return $aIncludes;
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
