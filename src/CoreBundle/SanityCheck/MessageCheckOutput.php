<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\SanityCheck;

use ChameleonSystem\SanityCheck\Formatter\OutputFormatterInterface;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use ChameleonSystem\SanityCheck\Output\AbstractTranslatingCheckOutput;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * DefaultCheckOutput is used to echo a CheckOutcome to the current output (usually browser or console).
 */
class MessageCheckOutput extends AbstractTranslatingCheckOutput
{
    const CONSUMER_NAME = 'chameleon_system_core.check.login';

    /**
     * @var OutputFormatterInterface
     */
    private $outputFormatter;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param string $translationDomain
     */
    public function __construct(RequestStack $requestStack, OutputFormatterInterface $outputFormatter, TranslatorInterface $translator, $translationDomain = 'chameleon_system_sanitycheck')
    {
        parent::__construct($translator, $translationDomain);
        $this->outputFormatter = $outputFormatter;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function gather(CheckOutcome $outcome)
    {
        $message = $this->getTranslatedMessage($outcome);
        $line = $this->outputFormatter->format($message, $outcome->getLevel());
        $line .= $this->outputFormatter->getNewlineDelimiter();

        /** @var Session $session */
        $session = $this->requestStack->getCurrentRequest()->getSession();
        /** @var FlashBagInterface $flashBag */
        $flashBag = $session->getFlashBag();
        $flashBag->add(self::CONSUMER_NAME, $line);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function commit()
    {
    }

    /**
     * @param OutputFormatterInterface $outputFormatter
     *
     * @return void
     */
    public function setOutputFormatter($outputFormatter)
    {
        $this->outputFormatter = $outputFormatter;
    }
}
