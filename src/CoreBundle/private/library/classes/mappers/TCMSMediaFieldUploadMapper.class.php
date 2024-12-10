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
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class TCMSMediaFieldUploadMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('oCategory', 'TdbCmsMediaTree', null, true);
        $oRequirements->NeedsSourceObject('bShowCategorySelector', 'Boolean');
        $oRequirements->NeedsSourceObject('sFieldName', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
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
     * @param string $fieldName
     *
     * @return string
     */
    public function getOpenUploadWindowJS($fieldName)
    {
        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        $isInModal = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($fieldName)."');";
        if (null !== $parentField && '' !== $parentField && '' === $isInModal) {
            $parentIFrame = $parentField . '_iframe';
            $js .= "saveCMSRegistryEntry('_parentIFrame','".TGlobal::OutJS($parentIFrame)."');
                    TCMSFieldPropertyTableCmsMediaOpenUploadWindow_".TGlobal::OutHTML($fieldName)."(document.cmseditform.".TGlobal::OutHTML($fieldName)."__cms_media_tree_id.value,'".TGlobal::OutHTML($parentIFrame)."');";
        } else {
            $js .= "TCMSFieldPropertyTableCmsMediaOpenUploadWindow_".TGlobal::OutHTML($fieldName)."(document.cmseditform.".TGlobal::OutHTML($fieldName)."__cms_media_tree_id.value);";
        }

        return $js;
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
