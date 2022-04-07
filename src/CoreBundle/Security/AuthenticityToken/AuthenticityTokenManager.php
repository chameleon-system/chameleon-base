<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Security\AuthenticityToken;

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * {@inheritdoc}
 */
class AuthenticityTokenManager implements AuthenticityTokenManagerInterface
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var bool
     */
    private $tokenEnabledInBackend;
    /**
     * @var bool
     */
    private $tokenEnabledInFrontend;

    /**
     * @param CsrfTokenManagerInterface   $csrfTokenManager
     * @param RequestInfoServiceInterface $requestInfoService
     * @param InputFilterUtilInterface    $inputFilterUtil
     * @param bool                        $tokenEnabledInBackend
     * @param bool                        $tokenEnabledInFrontend
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, RequestInfoServiceInterface $requestInfoService, InputFilterUtilInterface $inputFilterUtil, $tokenEnabledInBackend = CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN_IN_BACKEND, $tokenEnabledInFrontend = CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->requestInfoService = $requestInfoService;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->tokenEnabledInBackend = $tokenEnabledInBackend;
        $this->tokenEnabledInFrontend = $tokenEnabledInFrontend;
    }

    /**
     * {@inheritdoc}
     */
    public function isProtectionEnabled()
    {
        if (true === $this->requestInfoService->isBackendMode() || true === $this->requestInfoService->isCmsTemplateEngineEditMode()) {
            return $this->tokenEnabledInBackend;
        }

        return $this->tokenEnabledInFrontend;
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenValid()
    {
        if (false === $this->isProtectionEnabled()) {
            return true;
        }

        $submittedToken = $this->getSubmittedToken();

        return $this->csrfTokenManager->isTokenValid(new CsrfToken(AuthenticityTokenManagerInterface::TOKEN_ID, $submittedToken));
    }

    private function getSubmittedToken(): ?string
    {
        /** @var string|null $token */
        $token = $this->inputFilterUtil->getFilteredPostInput(AuthenticityTokenManagerInterface::TOKEN_ID);

        if (null === $token) {
            /** @var string|null $token */
            $token = $this->inputFilterUtil->getFilteredGetInput(AuthenticityTokenManagerInterface::TOKEN_ID);
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken()
    {
        $this->csrfTokenManager->refreshToken(AuthenticityTokenManagerInterface::TOKEN_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoredToken()
    {
        return $this->csrfTokenManager->getToken(AuthenticityTokenManagerInterface::TOKEN_ID)->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function addTokenToForms($string)
    {
        if (false === $this->isProtectionEnabled()) {
            return $string;
        }
        // We split the string into form elements in order to reduce work for preg_replace_callback().
        $parts = explode('</form>', $string);
        $partCount = \count($parts);
        if (1 === $partCount) {
            return $string;
        }

        try {
            $tokenInputField = $this->getResolvedTokenAsParameter(AuthenticityTokenManagerInterface::TOKEN_FORMAT_POST);
            $tokenInputField .= "\n";
        } catch (InvalidTokenFormatException $e) {
            // We're sure that the format is valid as we use a constant.
        }

        // The last block can never contain a form, so we can ignore it.
        $lastIndex = $partCount - 2;
        for ($i = 0; $i <= $lastIndex; ++$i) {
            $part = &$parts[$i];
            $pattern = '/(<(input|button)[^>]+module_fnc[^>]+>)/ui';
            if (1 === \preg_match($pattern, $part)) {
                $part .= $tokenInputField;
            }
            $error = \preg_last_error();
            if ($error > 0) {
                throw new TokenInjectionFailedException(sprintf('Error %s: %s', $error, \array_flip(\get_defined_constants(true)['pcre'])[$error]));
            }
        }

        return \implode('</form>', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenPlaceholderAsParameter($format = AuthenticityTokenManagerInterface::TOKEN_FORMAT_GET)
    {
        $tokenValue = sprintf('[{%s}]', AuthenticityTokenManagerInterface::TOKEN_ID);

        return $this->getAuthenticityTokenAsParameter($format, $tokenValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedTokenAsParameter($format = AuthenticityTokenManagerInterface::TOKEN_FORMAT_GET)
    {
        $tokenValue = $this->getStoredToken();

        return $this->getAuthenticityTokenAsParameter($format, $tokenValue);
    }

    /**
     * @param string $format
     * @param string $tokenValue
     *
     * @return array|string
     *
     * @throws InvalidTokenFormatException
     */
    private function getAuthenticityTokenAsParameter($format, $tokenValue)
    {
        $tokenId = AuthenticityTokenManagerInterface::TOKEN_ID;
        switch ($format) {
            case AuthenticityTokenManagerInterface::TOKEN_FORMAT_GET:
                return $tokenId.'='.$tokenValue;
            case AuthenticityTokenManagerInterface::TOKEN_FORMAT_POST:
                return '<input type="hidden" name="'.$tokenId.'" value="'.$tokenValue.'" />';
            case AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY:
                return [
                    $tokenId => $tokenValue,
                ];
            default:
                throw new InvalidTokenFormatException(sprintf(
                    'Invalid token format "%s" requested. See constants in AuthenticityTokenManagerInterface for allowed values.',
                    $format
                ));
        }
    }
}
