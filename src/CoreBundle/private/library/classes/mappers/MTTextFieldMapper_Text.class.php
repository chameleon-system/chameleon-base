<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTTextFieldMapper_Text extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('oTextModuleConfiguration', 'TdbModuleText');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var $oTextModuleConfiguration TdbModuleText */
        $oTextModuleConfiguration = $oVisitor->GetSourceObject('oTextModuleConfiguration');
        if ($oTextModuleConfiguration && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oTextModuleConfiguration->table, $oTextModuleConfiguration->id);
        }
        $oDownloadList = $oTextModuleConfiguration->GetDownloads('data_pool');
        if ($oDownloadList && $oDownloadList->Length() > 0) {
            $aLinkList = [];
            while ($oDownload = $oDownloadList->Next()) {
                if ($bCachingEnabled) {
                    $oCacheTriggerManager->addTrigger('cms_document', $oDownload->id);
                }
                $aLink = [];
                $aLink['sLinkURL'] = $oDownload->GetPlainDownloadLink();
                $aLink['sTitle'] = $oDownload->GetName().' '.TCMSDownloadFile::GetHumanReadableFileSize($oDownload->sqlData['filesize']);
                $aLinkList[] = $aLink;
            }
            $oVisitor->SetMappedValue('aLinkList', $aLinkList);
        }
        $oVisitor->SetMappedValue('sHeadline', $oTextModuleConfiguration->sqlData['name']);
        $oVisitor->SetMappedValue('sSubHeadLine', $oTextModuleConfiguration->sqlData['subheadline']);
        $oVisitor->SetMappedValue('sText', $oTextModuleConfiguration->GetTextField('content'));
    }
}
