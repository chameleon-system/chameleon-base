<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMediaFieldUploadMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oCategory', 'TdbCmsMediaTree', null, true);
        $oRequirements->NeedsSourceObject('bShowCategorySelector', 'Boolean');
        $oRequirements->NeedsSourceObject('sFieldName', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oCategory = $oVisitor->GetSourceObject('oCategory');
        $bShowCategorySelector = $oVisitor->GetSourceObject('bShowCategorySelector');
        $sFieldName = $oVisitor->GetSourceObject('sFieldName');

        $oVisitor->SetMappedValue('sFieldName', $sFieldName);
        $oVisitor->SetMappedValue('sSelectCategoryName', $sFieldName.'__cms_media_tree_id');
        if (!is_null($oCategory)) {
            $oVisitor->SetMappedValue('sCategoryDefaultId', $oCategory->id);
            $oVisitor->SetMappedValue('sDefaultCategory', $oCategory->fieldName);
        }
        $oVisitor->SetMappedValue('bShowCategorySelector', $bShowCategorySelector);
        $oVisitor->SetMappedValue('sCategorySelectOptionsHtml', $this->getCategorySelectOptionsHtml($oCategory, $bShowCategorySelector));
        $oVisitor->SetMappedValue('sUploadButton', TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.link.upload_and_assign_media'), 'javascript:'.$this->getOpenUploadWindowJS($sFieldName), 'fas fa-upload'));
    }

    /**
     * @param TdbCmsMediaTree|null $oCategory
     * @param bool                 $bShowCategorySelector
     *
     * @return string
     */
    protected function getCategorySelectOptionsHtml($oCategory, $bShowCategorySelector)
    {
        if (false === $bShowCategorySelector && null === $oCategory) {
            return '';
        }

        $oTreeSelect = new TCMRenderMediaTreeSelectBox();
        $sSelectedId = null;
        if ($oCategory) {
            $sSelectedId = $oCategory->id;
        }

        return $oTreeSelect->GetTreeOptions($sSelectedId, true);
    }

    /**
     * @param string $sFieldName
     *
     * @return string
     */
    public function getOpenUploadWindowJS($sFieldName)
    {
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($sFieldName)."');TCMSFieldPropertyTableCmsMediaOpenUploadWindow_".TGlobal::OutHTML($sFieldName).'(document.cmseditform.'.TGlobal::OutHTML($sFieldName).'__cms_media_tree_id.value);';

        return $js;
    }
}
