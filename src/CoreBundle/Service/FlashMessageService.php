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
    const SESSION_KEY_NAME = 'core/cmsmessagemanager';
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
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
        if (false === $currentRequest->hasSession()) {
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
        if (null === $this->getSession()) {
            return null;
        }
        if (false === $this->getSession()->has(self::SESSION_KEY_NAME)) {
            $this->getSession()->set(self::SESSION_KEY_NAME, new \TCMSMessageManager());
        }

        return $this->getSession()->get(self::SESSION_KEY_NAME, null);
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage($consumer, $code, array $parameter = array())
    {
        $this->getHandler()->AddMessage($consumer, $code, $parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function consumeMessages($consumer, $remove = true)
    {
        return $this->getHandler()->ConsumeMessages($consumer, $remove);
    }

    /**
     * {@inheritdoc}
     */
    public function renderMessages(
        $sConsumerName,
        $sViewName = null,
        $sViewType = null,
        array $aCallTimeVars = array(),
        $bRemove = true
    ) {
        return $this->getHandler()->RenderMessages($sConsumerName, $sViewName, $sViewType, $aCallTimeVars, $bRemove);
    }

    /**
     * {@inheritdoc}
     */
    public function clearMessages($sConsumerName = null)
    {
        $this->getHandler()->ClearMessages($sConsumerName);
    }

    /**
     * {@inheritdoc}
     */
    public function consumerHasMessages($sConsumerName)
    {
        return $this->getHandler()->ConsumerHasMessages($sConsumerName);
    }

    /**
     * {@inheritdoc}
     */
    public function consumerMessageCount($sConsumerName)
    {
        return $this->getHandler()->ConsumerMessageCount($sConsumerName);
    }

    /**
     * {@inheritdoc}
     */
    public function totalMessageCount()
    {
        return $this->getHandler()->TotalMessageCount();
    }

    /**
     * {@inheritdoc}
     */
    public function injectMessageIntoString($sText)
    {
        return $this->getHandler()->InjectMessageIntoString($sText);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerListWithMessages()
    {
        return $this->getHandler()->GetConsumerListWithMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassesForConsumer($sConsumerName, $sDivider = ' ')
    {
        return $this->getHandler()->GetClassesForConsumer($sConsumerName, $sDivider);
    }

    /**
     * {@inheritdoc}
     */
    public function addBackendToasterMessage($id, $type = 'ERROR', array $parameters = array(), $domain = TranslationConstants::DOMAIN_BACKEND)
    {
        $this->getHandler()->addBackendToasterMessage($id, $type, $parameters, $domain);
    }
}
