<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\Mapper;

use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSMediaFieldMapperDecorator implements \IViewMapper
{
    /**
     * @var \TCMSMediaFieldMapper
     */
    private $subject;

    /**
     * @var MediaManagerUrlGeneratorInterface
     */
    private $mediaManagerUrlGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        \TCMSMediaFieldMapper $subject,
        MediaManagerUrlGeneratorInterface $mediaManagerUrlGenerator,
        TranslatorInterface $translator)
    {
        $this->subject = $subject;
        $this->mediaManagerUrlGenerator = $mediaManagerUrlGenerator;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $oVisitor, $bCachingEnabled, \IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $this->subject->Accept($oVisitor, $bCachingEnabled, $oCacheTriggerManager);

        $mediaManagerButtonHtml = $this->getMediaManagerButtonHtml();
        $oVisitor->SetMappedValue('sHtmlManageMediaButton', $mediaManagerButtonHtml);
    }

    private function getMediaManagerButtonHtml(): string
    {
        $mediaManagerUrl = $this->mediaManagerUrlGenerator->getStandaloneMediaManagerUrl();
        $buttonTitle = $this->translator->trans('chameleon_system_core.link.open_media_manager');

        return \TCMSRender::DrawButton($buttonTitle, $mediaManagerUrl, 'far fa-image', null, null, null, null, '_blank');
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $this->subject->GetRequirements($oRequirements);
    }
}
