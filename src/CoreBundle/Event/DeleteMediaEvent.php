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

use Symfony\Component\EventDispatcher\Event;

class DeleteMediaEvent extends Event
{
    /**
     * @var string
     */
    private $deletedMediaId;

    /**
     * @param string $deletedMediaId
     */
    public function __construct($deletedMediaId)
    {
        $this->deletedMediaId = $deletedMediaId;
    }

    /**
     * @return string
     */
    public function getDeletedMediaId()
    {
        return $this->deletedMediaId;
    }
}
