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

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AddBackendToasterMessageListener
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $message
     * @param string $type
     */
    public function __construct($message, $type)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return void
     */
    public function addMessage(ResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $response = $event->getResponse();
        $content = $response->getContent();
        $pos = strripos($content, '</body>');
        if (false === $pos) {
            return;
        }
        $message = "<script type=\"text/javascript\">toasterMessage('{$this->message}', '{$this->type}');</script>";
        $content = substr($content, 0, $pos).$message.substr($content, $pos);
        $response->setContent($content);
    }
}
