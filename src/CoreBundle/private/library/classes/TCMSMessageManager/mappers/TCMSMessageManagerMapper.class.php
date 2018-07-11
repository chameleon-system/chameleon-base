<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMessageManagerMapper extends AbstractTCMSMessageManagerMapper
{
    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        /** @var $oMessageType TdbCmsMessageManagerMessageType */
        $oMessageType = $oVisitor->GetSourceObject('oMessageType');
        if ($oMessageType && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oMessageType->table, $oMessageType->id);
        }
        $sMessage = $oVisitor->GetSourceObject('sMessage');
        if ($oMessageType->fieldCmsMediaId) {
            $oMedia = TdbCmsMedia::GetNewInstance();
            if ($oMedia->Load($oMessageType->fieldCmsMediaId)) {
                $oImage = $oMedia->GetImage(0, 'cms_media_id');
                if ($bCachingEnabled) {
                    $oCacheTriggerManager->addTrigger($oMedia->table, $oMedia->id);
                }
                if ($oImage) {
                    $oVisitor->SetMappedValue('sMessageTypeIconSRC', $oImage->GetFullURL());
                }
            }
        }
        $oVisitor->SetMappedValue('sText', $sMessage);
        $oVisitor->SetMappedValue('sMessageTypeClass', $oMessageType->fieldClass);
        $oVisitor->SetMappedValue('sMessageTypeColor', $oMessageType->fieldColor);
        $oVisitor->SetMappedValue('sMessageTypeName', $oMessageType->fieldName);
    }
}
