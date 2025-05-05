<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\Service;

use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ExtranetUserProvider implements ExtranetUserProviderInterface
{
    public const SESSION_KEY_NAME = 'esono/pkgExtranet/frontendUser';

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
    public function getActiveUser()
    {
        $session = $this->getStartedSession();

        if (null === $session) {
            return \TdbDataExtranetUser::GetNewInstance();
        }

        $user = $session->get(self::SESSION_KEY_NAME);
        if (null === $user) {
            $this->reset();
        }

        return $session->get(self::SESSION_KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $session = $this->getStartedSession();
        if (null === $session) {
            return;
        }

        $session->set(self::SESSION_KEY_NAME, \TdbDataExtranetUser::GetNewInstance());
    }

    private function getStartedSession(): ?SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }
        if (false === $request->hasSession()) {
            return null;
        }
        if (false === $request->getSession()->isStarted()) {
            return null;
        }

        return $request->getSession();
    }
}
