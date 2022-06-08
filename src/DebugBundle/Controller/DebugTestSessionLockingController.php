<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DebugBundle\Controller;

use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DebugTestSessionLockingController
{
    /**
     * @var \TPKgCmsSession
     */
    private $session;
    /**
     * @var ChainRouter
     */
    private $router;

    public function __construct(\TPKgCmsSession $session, ChainRouter $router)
    {
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        return new Response('foo');
    }

    /**
     * @param string $value
     * @return RedirectResponse
     */
    public function setAction($value = 'no value set')
    {
        $this->session->set('testvalue', $value);

        return new RedirectResponse($this->router->generate('_debug_get'));
    }

    /**
     * @return Response
     */
    public function getAction()
    {
        $content = $this->session->get('testvalue');

        return new Response($content);
    }

    /**
     * @param int $duration
     * @return RedirectResponse
     */
    public function lockAction($duration)
    {
        $this->session->restartSessionWithWriteLock();
        sleep($duration);
        $this->session->set('testvalue', 'set from lock');

        return new RedirectResponse($this->router->generate('_debug_get'));
    }
}
