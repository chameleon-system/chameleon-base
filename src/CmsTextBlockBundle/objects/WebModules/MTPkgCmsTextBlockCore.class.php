<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * loads and renders a wysiwyg text block from pkg_cms_text_block based
 * usable for static text blocks like footer bars that is placed on every page.
 * /**/
class MTPkgCmsTextBlockCore extends TUserCustomModelBase
{
    /**
     * defines the identifier key of the text block that will be rendered
     * you need to set this in your extension or in the module config as parameter "sTextBlockkey".
     *
     * @var string
     */
    protected $sTextBlockKey = '';

    /**
     * holds the loaded text block db object.
     *
     * @var TdbPkgCmsTextBlock
     */
    protected $oPkgCmsTextBlock;

    public function Execute()
    {
        parent::Execute();
        $this->data['oPkgCmsTextBlock'] = $this->LoadTextBlock();

        return $this->data;
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
     * if the content that is to be cached comes from the database (as ist most often the case)
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
        $aTrigger = parent::_GetCacheTableInfos();
        if (!is_array($aTrigger)) {
            $aTrigger = [];
        }
        if (!is_null($this->oPkgCmsTextBlock)) {
            $aTrigger[] = ['table' => 'pkg_cms_text_block', 'id' => $this->oPkgCmsTextBlock->id];
        }

        return $aTrigger;
    }

    /**
     * loads the text block from database based on $this->sTextBlockKey or
     * backend module configuration and current portal.
     *
     * @return TdbPkgCmsTextBlock|false
     */
    protected function LoadTextBlock()
    {
        // try to load it from backend module config
        if (empty($this->sTextBlockKey) && array_key_exists('sTextBlockKey', $this->aModuleConfig)) {
            $this->sTextBlockKey = $this->aModuleConfig['sTextBlockKey'];
        }

        if (!empty($this->sTextBlockKey)) {
            $activePortal = $this->getPortalDomainService()->getActivePortal();
            $sQuery = "SELECT `pkg_cms_text_block`.* FROM `pkg_cms_text_block`
         LEFT JOIN `pkg_cms_text_block_cms_portal_mlt` ON `pkg_cms_text_block_cms_portal_mlt`.`source_id` = `pkg_cms_text_block`.`id`
         WHERE `pkg_cms_text_block_cms_portal_mlt`.`target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($activePortal->id)."'
         AND `pkg_cms_text_block`.`systemname` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sTextBlockKey)."'";
            $oTdbPkgCmsTextBlockList = TdbPkgCmsTextBlockList::GetList($sQuery);
            if ($oTdbPkgCmsTextBlockList->Length() > 0) {
                $this->oPkgCmsTextBlock = $oTdbPkgCmsTextBlockList->Current();
            }
        }

        return $this->oPkgCmsTextBlock;
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
