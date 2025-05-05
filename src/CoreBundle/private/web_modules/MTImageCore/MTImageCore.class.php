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
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;

/**
 * Used to display a single image.
 * /**/
class MTImageCore extends TUserCustomModelBase
{
    /**
     * holds the record from the module_single_dynamic_page.
     *
     * @var TdbModuleImage
     */
    public $_oTableRow;

    protected $bAllowHTMLDivWrapping = true;

    public function Execute()
    {
        $this->data = parent::Execute();
        $this->LoadTableRow();
        $oImages = $this->_oTableRow->GetImages('images');
        $oImage = $oImages->Current();

        // get page link as well
        $treeNodeId = $this->_oTableRow->sqlData['cms_tree_id'];
        $link = '';
        if (!empty($treeNodeId)) {
            $treeService = $this->getTreeService();
            $treeNode = $treeService->getById($treeNodeId);

            if (null !== $treeNode) {
                $link = $treeService->getLinkToPageForTreeRelative($treeNode);
            }
        }
        $this->data = ['oTableRow' => $this->_oTableRow, 'oImage' => $oImage, 'sLink' => $link];

        return $this->data;
    }

    protected function LoadTableRow()
    {
        if (null === $this->_oTableRow) {
            $oModuleImage = TdbModuleImage::GetNewInstance();
            $oModuleImage->SetLanguage($this->getLanguageService()->getActiveLanguageId());
            $oModuleImage->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
            $this->_oTableRow = $oModuleImage;
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
        $tableInfo[] = ['table' => 'module_image', 'id' => $this->_oTableRow->id];

        $this->LoadTableRow();
        $oImages = $this->_oTableRow->GetImages();
        while ($oImage = $oImages->Next()) {
            $tableInfo[] = ['table' => 'cms_media', 'id' => $oImage->id];
        }

        return $tableInfo;
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return TreeServiceInterface
     */
    private function getTreeService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.tree_service');
    }
}
