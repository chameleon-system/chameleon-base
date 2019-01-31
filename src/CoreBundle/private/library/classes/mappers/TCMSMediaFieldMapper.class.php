<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMediaFieldMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('sHtmlHiddenFields', 'String');
        $oRequirements->NeedsSourceObject('sFieldName', 'string');
        $oRequirements->NeedsSourceObject('sTableId', 'string');
        $oRequirements->NeedsSourceObject('sRecordId', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        /** @var $oTextModuleConfiguration TdbCmsTblConf */
        $sFieldName = $oVisitor->GetSourceObject('sFieldName');
        $sHtmlHiddenFields = $oVisitor->GetSourceObject('sHtmlHiddenFields');
        $sRecordId = $oVisitor->GetSourceObject('sRecordId');
        $sTableId = $oVisitor->GetSourceObject('sTableId');
        $sHTMLManageMediaButton = TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.link.open_media_manager'), "javascript:loadMediaManager('".$sRecordId."','".$sTableId."','".$sFieldName."');", 'far fa-image');
        $oVisitor->SetMappedValue('sHtmlManageMediaButton', $sHTMLManageMediaButton);
        $oVisitor->SetMappedValue('sFieldName', $sFieldName);
        $oVisitor->SetMappedValue('sRecordId', $sRecordId);
        $oVisitor->SetMappedValue('sTableId', $sTableId);
        $oVisitor->SetMappedValue('sHtmlHiddenFields', $sHtmlHiddenFields);
    }
}
