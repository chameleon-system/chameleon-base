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
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly RequestInfoServiceInterface $requestInfoService,
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly bool $tokenEnabledInBackend = CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN_IN_BACKEND,
        private readonly bool $tokenEnabledInFrontend = CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN)
    {
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
        $token = $this->inputFilterUtil->getFilteredPostInput(AuthenticityTokenManagerInterface::TOKEN_ID);

        if (null === $token) {
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
            $pattern = '/(<(input|button)[^>]+module_fnc[^>]+>)/ui';
            if (1 === \preg_match($pattern, $parts[$i])) {
                $parts[$i] .= $tokenInputField;
            }
            $error = \preg_last_error();
            if ($error > 0) {
                $errorName = $this->getRegeExErrorMessageByErrorCode($error);

                throw new TokenInjectionFailedException(sprintf('Error %s: %s', $error, $errorName));
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
     * @throws InvalidTokenFormatException
     *
     * @psalm-param AuthenticityTokenManagerInterface::TOKEN_FORMAT_* $format
     *
     * @psalm-suppress NoValue
     */
    private function getAuthenticityTokenAsParameter(int $format, string $tokenValue): array|string
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

    private function getRegeExErrorMessageByErrorCode(int $errorCode): string
    {
        $pcreConstants = \get_defined_constants(true)['pcre'];

        $filteredConstants = array_filter($pcreConstants, function ($value) {
            return is_int($value) || is_string($value);
        });

        $flippedConstants = array_flip($filteredConstants);

        return isset($flippedConstants[$errorCode]) ? $flippedConstants[$errorCode] : 'Unknown error';
    }
}
