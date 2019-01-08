<?php

namespace ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog;

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function __invoke(array $record)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return $record;
        }

        if (null === $request->getSession()) {
            return $record;
        }

        $sessionId = $request->getSession()->getId();

        if (true === \array_key_exists('extra', $record)) {
            $record['extra']['session_id'] = $sessionId;
        } else {
            $record['extra'] = ['session_id' => $sessionId];
        }

        return $record;
    }
}
