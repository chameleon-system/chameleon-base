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

/**
 * @psalm-suppress InvalidReturnStatement, InvalidReturnType
 *
 * @FIXME Default value of `$content` is an empty array, when everything outside of this class expects `getContent` to return a string.
 */
class ResourceCollectionJavaScriptCollectedEvent extends Event implements ResourceCollectionJavaScriptCollectedEventInterface
{
    /**
     * @var array|string
     */
    private $content = [];

    /**
     * @param string|null $content
     */
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
