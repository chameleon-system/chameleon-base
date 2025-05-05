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

class ChangeActiveLanguagesForPortalEvent extends Event
{
    /**
     * @var \TCMSPortal
     */
    private $portal;
    /**
     * @var string[]
     */
    private $oldLanguages;
    /**
     * @var string[]
     */
    private $newLanguages;

    /**
     * @param string[] $oldLanguages list of language IDs for languages that were active before the change
     * @param string[] $newLanguages list of language IDs for languages that are active before the change
     */
    public function __construct(\TCMSPortal $portal, array $oldLanguages, array $newLanguages)
    {
        $this->portal = $portal;
        $this->oldLanguages = $oldLanguages;
        $this->newLanguages = $newLanguages;
    }

    /**
     * @return \TCMSPortal
     */
    public function getPortal()
    {
        return $this->portal;
    }

    /**
     * @return string[]
     */
    public function getOldLanguages()
    {
        return $this->oldLanguages;
    }

    /**
     * @return string[]
     */
    public function getNewLanguages()
    {
        return $this->newLanguages;
    }
}
