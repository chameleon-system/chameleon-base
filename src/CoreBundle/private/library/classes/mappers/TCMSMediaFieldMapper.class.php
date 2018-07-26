<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TCMSMediaFieldMapper extends AbstractViewMapper
{
    /**
     * @var MediaManagerUrlGeneratorInterface
     */
    private $mediaManagerUrlGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param MediaManagerUrlGeneratorInterface $mediaManagerUrlGenerator
     * @param TranslatorInterface               $translator
     */
    public function __construct(
        MediaManagerUrlGeneratorInterface $mediaManagerUrlGenerator,
        TranslatorInterface $translator)
    {
        $this->mediaManagerUrlGenerator = $mediaManagerUrlGenerator;
        $this->translator = $translator;
    }

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
        $sFieldName = $oVisitor->GetSourceObject('sFieldName');
        $sHtmlHiddenFields = $oVisitor->GetSourceObject('sHtmlHiddenFields');
        $sRecordId = $oVisitor->GetSourceObject('sRecordId');
        $sTableId = $oVisitor->GetSourceObject('sTableId');

        $mediaManagerButtonHtml = $this->getMediaManagerButtonHtml();
        $oVisitor->SetMappedValue('sHtmlManageMediaButton', $mediaManagerButtonHtml);
        $oVisitor->SetMappedValue('sFieldName', $sFieldName);
        $oVisitor->SetMappedValue('sRecordId', $sRecordId);
        $oVisitor->SetMappedValue('sTableId', $sTableId);
        $oVisitor->SetMappedValue('sHtmlHiddenFields', $sHtmlHiddenFields);
    }

    /**
     * @return string
     */
    private function getMediaManagerButtonHtml(): string
    {
        $mediaManagerUrl = $this->mediaManagerUrlGenerator->getStandaloneMediaManagerUrl();
        $buttonTitle = $this->translator->trans('chameleon_system_core.link.open_media_manager');

        if (true === $this->mediaManagerUrlGenerator->openStandaloneMediaManagerInNewWindow()) {
            $onClickEvent = "window.open('".$mediaManagerUrl."','mediaManager','_blank'); return false;";

            return TCMSRender::DrawButton($buttonTitle, '#', URL_CMS.'/images/icons/image.gif', null, null, null, $onClickEvent);
        } else {
            return TCMSRender::DrawButton($buttonTitle, $mediaManagerUrl, URL_CMS.'/images/icons/image.gif', null, null, null,null, '_blank');
        }
    }
}
