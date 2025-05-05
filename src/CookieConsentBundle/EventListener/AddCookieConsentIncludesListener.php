<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CookieConsentBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\HtmlIncludeEventInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AddCookieConsentIncludesListener
{
    /**
     * @var string
     */
    private $position;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $bgColor;

    /**
     * @var string
     */
    private $buttonBgColor;

    /**
     * @var string
     */
    private $buttonTextColor;

    /**
     * @var string
     */
    private $privacyPolicySystemPageName;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var SystemPageServiceInterface
     */
    private $systemPageService;

    /**
     * @param string $position
     * @param string $theme
     * @param string $bgColor
     * @param string $buttonBgColor
     * @param string $buttonTextColor
     * @param string $privacyPolicySystemPageName
     */
    public function __construct(
        $position,
        $theme,
        $bgColor,
        $buttonBgColor,
        $buttonTextColor,
        $privacyPolicySystemPageName,
        RequestInfoServiceInterface $requestInfoService,
        Environment $twig,
        SystemPageServiceInterface $systemPageService
    ) {
        $this->position = $position;
        $this->theme = $theme;
        $this->bgColor = $bgColor;
        $this->buttonBgColor = $buttonBgColor;
        $this->buttonTextColor = $buttonTextColor;
        $this->privacyPolicySystemPageName = $privacyPolicySystemPageName;
        $this->requestInfoService = $requestInfoService;
        $this->twig = $twig;
        $this->systemPageService = $systemPageService;
    }

    /**
     * @return void
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event)
    {
        if (true === $this->requestInfoService->isBackendMode()) {
            return;
        }

        $includes[] = $this->twig->render('CookieConsent/header.html.twig');
        $event->addData($includes);
    }

    /**
     * @return void
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onGlobalHtmlFooterInclude(HtmlIncludeEventInterface $event)
    {
        if (true === $this->requestInfoService->isBackendMode()) {
            return;
        }

        $templateVars = [
            'position' => $this->position,
            'theme' => $this->theme,
            'bgcolor' => $this->bgColor,
            'buttonbgcolor' => $this->buttonBgColor,
            'buttontextcolor' => $this->buttonTextColor,
            'morelinkurl' => $this->getPrivacyPolicyPageUrl(),
        ];

        $includes = [
            $this->twig->render('CookieConsent/footer.html.twig', $templateVars),
        ];
        $event->addData($includes);
    }

    /**
     * @return string
     */
    private function getPrivacyPolicyPageUrl()
    {
        try {
            return $this->systemPageService->getLinkToSystemPageRelative($this->privacyPolicySystemPageName);
        } catch (RouteNotFoundException $e) {
            return '';
        }
    }
}
