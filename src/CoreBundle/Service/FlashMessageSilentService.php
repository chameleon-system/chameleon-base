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

/**
 * Silent service can be used when no session is available and no messages need to be printed.
 * Flash messages are not generated in sleep mode.
 */
class FlashMessageSilentService implements FlashMessageServiceInterface
{

    private FlashMessageServiceInterface $subject;

    private bool $isSilentModeActive = false;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(FlashMessageServiceInterface $subject)
    {
        $this->subject = $subject;
    }

    public function setSilentMode(bool $silentMode): void
    {
        $this->isSilentModeActive = $silentMode;
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage($consumer, $code, array $parameter = array())
    {
        if (true === $this->isSilentModeActive) {
            return;
        }
        $this->subject->AddMessage($consumer, $code, $parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function consumeMessages($consumer, $remove = true, bool $includeGlobal = true)
    {
        if (true === $this->isSilentModeActive) {
            return new \TIterator();
        }

        return $this->subject->ConsumeMessages($consumer, $remove, $includeGlobal);
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
        if (true === $this->isSilentModeActive) {
            return '';
        }

        return $this->subject->RenderMessages($sConsumerName, $sViewName, $sViewType, $aCallTimeVars, $bRemove);
    }

    /**
     * {@inheritdoc}
     */
    public function clearMessages($sConsumerName = null)
    {
        if (true === $this->isSilentModeActive) {
            return;
        }
        $this->subject->ClearMessages($sConsumerName);
    }

    /**
     * {@inheritdoc}
     */
    public function consumerHasMessages($sConsumerName, bool $includeGlobal = true)
    {
        if (true === $this->isSilentModeActive) {
            return false;
        }

        return $this->subject->ConsumerHasMessages($sConsumerName, $includeGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function consumerMessageCount($sConsumerName, bool $includeGlobal = true)
    {
        if (true === $this->isSilentModeActive) {
            return 0;
        }

        return $this->subject->ConsumerMessageCount($sConsumerName, $includeGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function totalMessageCount()
    {
        if (true === $this->isSilentModeActive) {
            return 0;
        }

        return $this->subject->TotalMessageCount();
    }

    /**
     * {@inheritdoc}
     */
    public function injectMessageIntoString($sText)
    {
        if (true === $this->isSilentModeActive) {
            return $sText;
        }

        return $this->subject->InjectMessageIntoString($sText);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerListWithMessages()
    {
        if (true === $this->isSilentModeActive) {
            return [];
        }

        return $this->subject->GetConsumerListWithMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassesForConsumer($sConsumerName, $sDivider = ' ')
    {
        if (true === $this->isSilentModeActive) {
            return '';
        }

        return $this->subject->GetClassesForConsumer($sConsumerName, $sDivider);
    }

    /**
     * {@inheritdoc}
     */
    public function addBackendToasterMessage(
        $id,
        $type = 'ERROR',
        array $parameters = array(),
        $domain = TranslationConstants::DOMAIN_BACKEND
    ) {
        if (true === $this->isSilentModeActive) {
            return;
        }
        $this->subject->addBackendToasterMessage($id, $type, $parameters, $domain);
    }
}
