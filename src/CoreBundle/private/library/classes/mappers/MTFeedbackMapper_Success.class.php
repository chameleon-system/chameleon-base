<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTFeedbackMapper_Success extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oFeedbackModuleConfiguration', 'TdbModuleFeedback');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        /** @var $oFeedbackModuleConfiguration TdbModuleFeedback */
        $oFeedbackModuleConfiguration = $oVisitor->GetSourceObject('oFeedbackModuleConfiguration');
        if ($oFeedbackModuleConfiguration && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oFeedbackModuleConfiguration->table, $oFeedbackModuleConfiguration->id);
        }
        $oVisitor->SetMappedValue('sHeadline', $oFeedbackModuleConfiguration->fieldName);
        $oVisitor->SetMappedValue('sText', $oFeedbackModuleConfiguration->GetTextField('done_text'));
    }
}
