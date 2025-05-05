<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TMapper_ViewPortSwitch extends AbstractViewMapper
{
    /**
     * @var ActivePageServiceInterface
     */
    private $activePageService;
    /**
     * @var TCMSViewPortManager
     */
    private $viewPortManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(?ActivePageServiceInterface $activePageService = null, ?TCMSViewPortManager $viewPortManager = null, ?TranslatorInterface $translator = null)
    {
        if (null === $activePageService) {
            $this->activePageService = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
        } else {
            $this->activePageService = $activePageService;
        }
        if (null === $viewPortManager) {
            $this->viewPortManager = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.view_port_manager');
        } else {
            $this->viewPortManager = $viewPortManager;
        }
        if (null === $translator) {
            $this->translator = ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
        } else {
            $this->translator = $translator;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject(
            'oActivePAge',
            'TCMSActivePage',
            $this->activePageService->getActivePage()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $oActivePage = $oVisitor->GetSourceObject('oActivePAge');
        if (true === $this->viewPortManager->isDesktopViewPort()) {
            $sLinkText = $this->translator->trans('chameleon_system_core.mobile.mobile_version');
            $sLinkURL = $oActivePage->GetRealURL(['showDesktopMode' => false]);
        } else {
            $sLinkText = $this->translator->trans('chameleon_system_core.mobile.desktop_version');
            $sLinkURL = $oActivePage->GetRealURL(['showDesktopMode' => true]);
            $oVisitor->SetMappedValue('additionalCssClasses', 'hidden-sm hidden-md hidden-lg');
        }
        $oVisitor->SetMappedValue('sTitle', $sLinkText);
        $oVisitor->SetMappedValue('sLinkURL', $sLinkURL);
    }
}
