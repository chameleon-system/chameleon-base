<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashMessageService implements FlashMessageServiceInterface
{
    public const SESSION_KEY_NAME = 'core/cmsmessagemanager';
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return SessionInterface|null
     */
    private function getSession()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (null === $currentRequest || false === $currentRequest->hasSession()) {
            return null;
        }

        $session = $currentRequest->getSession();
        if (false === $session->isStarted()) {
            return null;
        }

        return $session;
    }

    /**
     * @return \TCMSMessageManager|null
     */
    private function getHandler()
    {
        $session = $this->getSession();

        if (null === $session) {
            return null;
        }

        if (false === $session->has(self::SESSION_KEY_NAME)) {
            $session->set(self::SESSION_KEY_NAME, new \TCMSMessageManager());
        }

        return $session->get(self::SESSION_KEY_NAME, null);
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage($consumer, $code, array $parameter = [])
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return;
        }

        $handler->AddMessage($consumer, $code, $parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function consumeMessages($consumer, $remove = true, bool $includeGlobal = true)
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return new \TIterator();
        }

        return $handler->ConsumeMessages($consumer, $remove, $includeGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function renderMessages(
        $sConsumerName,
        $sViewName = null,
        $sViewType = null,
        array $aCallTimeVars = [],
        $bRemove = true
    ) {
        $handler = $this->getHandler();
        if (null === $handler) {
            return '';
        }

        return $handler->RenderMessages($sConsumerName, $sViewName, $sViewType, $aCallTimeVars, $bRemove);
    }

    /**
     * {@inheritdoc}
     */
    public function clearMessages($sConsumerName = null)
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return;
        }

        $handler->ClearMessages($sConsumerName);
    }

    /**
     * {@inheritdoc}
     */
    public function consumerHasMessages($sConsumerName, bool $includeGlobal = true)
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return false;
        }

        return $handler->ConsumerHasMessages($sConsumerName, $includeGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function consumerMessageCount($sConsumerName, bool $includeGlobal = true)
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return 0;
        }

        return $handler->ConsumerMessageCount($sConsumerName, $includeGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function totalMessageCount()
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return 0;
        }

        return $handler->TotalMessageCount();
    }

    /**
     * {@inheritdoc}
     */
    public function injectMessageIntoString($sText)
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return $sText;
        }

        return $handler->InjectMessageIntoString($sText);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerListWithMessages()
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return [];
        }

        return $handler->GetConsumerListWithMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassesForConsumer($sConsumerName, $sDivider = ' ')
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return '';
        }

        return $handler->GetClassesForConsumer($sConsumerName, $sDivider);
    }

    /**
     * {@inheritdoc}
     */
    public function addBackendToasterMessage($id, $type = 'ERROR', array $parameters = [], $domain = TranslationConstants::DOMAIN_BACKEND)
    {
        $handler = $this->getHandler();
        if (null === $handler) {
            return;
        }

        $handler->addBackendToasterMessage($id, $type, $parameters, $domain);
    }
}
