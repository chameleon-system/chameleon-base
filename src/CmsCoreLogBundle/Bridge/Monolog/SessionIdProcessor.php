<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionIdProcessor implements ProcessorInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(LogRecord $record)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return $record;
        }

        if (false === $request->hasSession()) {
            return $record;
        }

        $sessionId = $request->getSession()->getId();

        if (true === \array_key_exists('extra', $record->toArray())) {
            $record['extra']['session_id'] = $sessionId;
        } else {
            $record['extra'] = ['session_id' => $sessionId];
        }

        return $record;
    }
}
