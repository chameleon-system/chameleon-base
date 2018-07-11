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
    const SESSION_KEY_NAME = 'esono/pkgExtranet/frontendUser';

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
     * {@inheritdoc}
     */
    public function getActiveUser()
    {
        if (false === $this->hasSession() || false === $this->sessionIsStarted()) {
            return null;
        }

        $user = $this->getSession()->get(self::SESSION_KEY_NAME);
        if (null === $user) {
            $this->reset();
        }

        return $this->getSession()->get(self::SESSION_KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->getSession()->set(self::SESSION_KEY_NAME, \TdbDataExtranetUser::GetNewInstance());
    }

    /**
     * @return null|SessionInterface
     */
    private function getSession()
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }

    /**
     * @return bool
     */
    private function hasSession()
    {
        $hasSession = false;
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $hasSession = $request->hasSession();
        }

        return $hasSession;
    }

    /**
     * @return bool
     */
    private function sessionIsStarted()
    {
        return $this->getSession()->isStarted();
    }
}
