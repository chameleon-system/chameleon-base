<?php

namespace ChameleonSystem\CoreBundle\RequestState;

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateElementProviderInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\ExtranetBundle\ExtranetEvents;
use Symfony\Component\HttpFoundation\Request;

class CoreStateElementProvider implements RequestStateElementProviderInterface
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        LanguageServiceInterface $languageService
    ) {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateElements(Request $request)
    {
        $activeDomain = $this->portalDomainService->getActiveDomain();

        return [
            '_bForceNonSSLMediaURLs' => \TCMSImageEndpoint::ForceNonSSLURLs(),
            'sOriginalDomainName' => (null !== $activeDomain) ? $activeDomain->id : null,
            '_cms_use_ssl_page' => $request->isSecure(),
            '_cms_current_language_id' => $this->languageService->getActiveLanguageId(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getResetStateEvents()
    {
        return [
            CoreEvents::LOCALE_CHANGED,
            ExtranetEvents::USER_LOGIN_SUCCESS,
            ExtranetEvents::USER_LOGOUT_SUCCESS,
        ];
    }
}
