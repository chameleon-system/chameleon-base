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
use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TPkgNewsletterMapper_QuickSignup extends AbstractViewMapper
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var SystemPageServiceInterface
     */
    private $systemPageService;

    /**
     * @param PortalDomainServiceInterface|null $portalDomainService
     * @param SystemPageServiceInterface|null   $systemPageService
     */
    public function __construct(PortalDomainServiceInterface $portalDomainService = null, SystemPageServiceInterface $systemPageService = null)
    {
        if (null === $portalDomainService) {
            $this->portalDomainService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
        } else {
            $this->portalDomainService = $portalDomainService;
        }
        if (null === $systemPageService) {
            $this->systemPageService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
        } else {
            $this->systemPageService = $systemPageService;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oPortal', 'TdbCmsPortal', null, true);
        $oRequirements->NeedsSourceObject('sModuleSpotName', '', 'spota');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oPortal = $oVisitor->GetSourceObject('oPortal');
        if (null === $oPortal) {
            $oPortal = $this->portalDomainService->getActivePortal();
        }
        try {
            $sNewsletterLink = $this->systemPageService->getLinkToSystemPageRelative('newsletter');
        } catch (RouteNotFoundException $e) {
            $sNewsletterLink = '';
        }

        $oVisitor->SetMappedValue('sNewsletterLink', $sNewsletterLink);
        $oVisitor->SetMappedValue('sModuleSpotName', $oVisitor->GetSourceObject('sModuleSpotName'));
        $oVisitor->SetMappedValue('sFieldNamesPrefix', MTPkgNewsletterSignupCore::INPUT_DATA_NAME);

        $aFieldSalutation['aValueList'] = $this->getSalutations();
        $oVisitor->SetMappedValue('aFieldSalutation', $aFieldSalutation);

        if ($bCachingEnabled) {
            $oCacheTriggerManager->addTrigger('cms_portal', $oPortal->id);
        }
    }

    /**
     * loads the list of salutations.
     *
     * @return array
     */
    protected function getSalutations()
    {
        $aValueList = array();
        $oSalutationList = TdbDataExtranetSalutationList::GetList();
        while ($oSalutation = &$oSalutationList->Next()) {
            $aValueList[] = array(
                'sValue' => $oSalutation->id,
                'sName' => $oSalutation->GetName(),
            );
        }

        return $aValueList;
    }
}
