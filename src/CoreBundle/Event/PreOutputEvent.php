<?php

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class PreOutputEvent extends Event
{
    /**
     * @var string
     */
    private $content;
    /**
     * @var Request
     */
    private $request;

    public function __construct(string $content, Request $request)
    {
        $this->content = $content;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }


}
