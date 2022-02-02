<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ResourceCollectionJavaScriptCollectedEvent extends Event implements ResourceCollectionJavaScriptCollectedEventInterface
{
    /**
     * @var string
     */
    private $content = array();

    public function __construct($content = null)
    {
        if (null !== $content) {
            $this->setContent($content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }
}
