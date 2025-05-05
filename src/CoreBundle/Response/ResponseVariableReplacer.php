<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Response;

use ChameleonSystem\CoreBundle\Event\FilterContentEvent;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\TokenInjectionFailedException;

class ResponseVariableReplacer implements ResponseVariableReplacerInterface
{
    /**
     * @var AuthenticityTokenManagerInterface
     */
    private $authenticityTokenManager;
    /**
     * @var FlashMessageServiceInterface
     */
    private $flashMessageService;
    /**
     * @var array
     */
    private $variables = [];

    public function __construct(AuthenticityTokenManagerInterface $authenticityTokenManager, FlashMessageServiceInterface $flashMessageService)
    {
        $this->authenticityTokenManager = $authenticityTokenManager;
        $this->flashMessageService = $flashMessageService;
    }

    /**
     * {@inheritdoc}
     */
    public function addVariable(string $key, string $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * @return void
     *
     * @throws TokenInjectionFailedException
     */
    public function handleResponse(FilterContentEvent $event)
    {
        $event->setContent($this->replaceVariables($event->getContent()));
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType, InvalidReturnStatement - We know that the return type is correct here.
     */
    public function replaceVariables($content)
    {
        if (true === \is_object($content)) {
            foreach ($content as $sProperty => $sValue) {
                $content->{$sProperty} = $this->replaceVariables($sValue);
            }
        } elseif (true === \is_array($content)) {
            foreach ($content as $sKey => $sValue) {
                $content[$sKey] = $this->replaceVariables($sValue);
            }
        } elseif (true === \is_string($content)) {
            $content = $this->doReplaceVariables((string) $content);
        }

        return $content;
    }

    /**
     * @throws TokenInjectionFailedException
     */
    private function doReplaceVariables(string $content): string
    {
        $content = $this->flashMessageService->injectMessageIntoString($content);
        $content = $this->authenticityTokenManager->addTokenToForms($content);
        $this->variables[AuthenticityTokenManagerInterface::TOKEN_ID] = $this->authenticityTokenManager->getStoredToken();
        $oStringReplace = new \TPkgCmsStringUtilities_VariableInjection();
        $content = $oStringReplace->replace($content, $this->variables);

        return $content;
    }
}
