<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\JavascriptPlugin;

/**
 * Represents a media tree node as JSON object.
 */
class MediaTreeNodeJsonObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $icon;

    /**
     * @var string
     */
    public $message = '';

    /**
     * MediaTreeNodeJsonObject constructor.
     *
     * @param string $id
     * @param string $name
     * @param string|null $icon
     */
    public function __construct($id, $name, $icon = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
