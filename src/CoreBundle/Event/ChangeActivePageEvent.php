<?php

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use TCMSActivePage;

class ChangeActivePageEvent extends Event
{
    /**
     * @var TCMSActivePage
     */
    private $newActivePage;
    /**
     * @var TCMSActivePage|null
     */
    private $oldActivePage;

    /**
     * @return TCMSActivePage
     */
    public function getNewActivePage()
    {
        return $this->newActivePage;
    }

    /**
     * @return TCMSActivePage|null
     */
    public function getOldActivePage()
    {
        return $this->oldActivePage;
    }

    /**
     * @param TCMSActivePage      $newActivePage
     * @param TCMSActivePage|null $oldActivePage
     */
    public function __construct(TCMSActivePage $newActivePage, TCMSActivePage $oldActivePage = null)
    {
        $this->newActivePage = $newActivePage;
        $this->oldActivePage = $oldActivePage;
    }
}
