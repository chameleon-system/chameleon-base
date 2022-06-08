<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Generates a new session ID after login. Note that this class mimics Symfony behavior in deleting session data
 * immediately, contradicting the PHP docs (https://secure.php.net/manual/en/features.session.security.management.php).
 */
class MigrateSessionListener
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
     * @return void
     */
    public function migrateSession()
    {
        $session = $this->requestStack->getMasterRequest()->getSession();
        if (null === $session) {
            return;
        }

        $session->migrate(true);
    }
}
