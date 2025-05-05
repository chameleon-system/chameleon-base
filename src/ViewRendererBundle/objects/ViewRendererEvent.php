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

use Symfony\Contracts\EventDispatcher\Event;

class ViewRendererEvent extends Event
{
    public const EVENT_POST_RENDER = 'chameleon_system.viewrenderer.post_render';

    /**
     * @var string
     */
    private $content;
    /**
     * @var class-string<\IViewMapper>[]
     */
    private $mappers;
    /**
     * @var string
     */
    private $viewName;

    /**
     * @param string $content
     * @param class-string<\IViewMapper>[] $mappers
     * @param string $viewName
     */
    public function __construct($content, array $mappers, $viewName)
    {
        $this->content = $content;
        $this->mappers = $mappers;
        $this->viewName = $viewName;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return class-string<\IViewMapper>[]
     */
    public function getMappers()
    {
        return $this->mappers;
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return $this->viewName;
    }
}
