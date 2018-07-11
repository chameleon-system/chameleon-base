<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\objects;

use Symfony\Component\EventDispatcher\Event;

class ViewRendererEvent extends Event
{
    const EVENT_POST_RENDER = 'chameleon_system.viewrenderer.post_render';

    private $content;
    /**
     * @var array
     */
    private $mappers;
    /**
     * @var
     */
    private $viewName;

    public function __construct($content, array $mappers, $viewName)
    {
        $this->content = $content;
        $this->mappers = $mappers;
        $this->viewName = $viewName;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getMappers()
    {
        return $this->mappers;
    }

    /**
     * @return mixed
     */
    public function getViewName()
    {
        return $this->viewName;
    }
}
