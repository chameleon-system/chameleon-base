<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ChameleonRedirectStrategyRegisterShutdown implements ChameleonRedirectStrategyInterface
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
     * @param string $url
     * @param int $status
     *
     * @return never-returns
     */
    public function redirect($url, $status)
    {
        /*
         * if we sent the header directly, then the users browser will start the redirect PARALLEL to the execution of the shutdown functions followed by session close
         * which MAY result in a session read after the redirect BEFORE the current call had a chance to write the session again.
         * we can avoid this by
         * a) registering a shutdown function LAST in the shutdown function chain
         * b) using this last shutdown function to commit.
         */
        register_shutdown_function(
            function ($url, $status, Request $request) {
                register_shutdown_function(
                    function ($url, $status, Request $request) {
                        if (true === $request->hasSession()) {
                            /** @var TPKgCmsSession $session */
                            $session = $request->getSession();
                            $session->save();
                        }
                        header('Location: '.$url, true, $status);
                    }, $url, $status, $request);
            }, $url, $status, $this->requestStack->getCurrentRequest()
        );
        exit;
    }
}
