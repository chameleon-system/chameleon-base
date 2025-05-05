<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * use the event manager to trigger new events, or register observers for events - implements an observer pattern.
 * /**/
class TPkgCmsEventManager
{
    /**
     * @var array<string, IPkgCmsEventObserver[]>
     */
    private $aObserver = [];

    /**
     * @return TPkgCmsEventManager
     *                             get the event manager instance - always use this method to instantiate
     */
    public static function GetInstance()
    {
        static $oInstance = null;
        if (is_null($oInstance)) {
            $oInstance = new self();
        }

        return $oInstance;
    }

    /**
     * @param string $sEventContext - IPkgCmsEvent::CONTEXT_* or TPkgCmsEvent::CONTEXT_*
     * @param string $sEventName - use IPkgCmsEvent::NAME_* or TPkgCmsEvent::NAME_*
     *
     * @return void
     */
    public function RegisterObserver($sEventContext, $sEventName, IPkgCmsEventObserver $oObserver)
    {
        $sFullEventName = $this->GetFullEventName($sEventContext, $sEventName);
        if (!isset($this->aObserver[$sFullEventName])) {
            $this->aObserver[$sFullEventName] = [];
        }
        $this->aObserver[$sFullEventName][] = $oObserver;
    }

    /**
     * @return IPkgCmsEvent
     */
    public function NotifyObservers(IPkgCmsEvent $oEvent)
    {
        $sEventClassName = $this->GetFullEventNameFromEvent($oEvent);
        if (isset($this->aObserver[$sEventClassName])) {
            reset($this->aObserver[$sEventClassName]);
            foreach (array_keys($this->aObserver[$sEventClassName]) as $sObserverIndex) {
                $oEvent = $this->aObserver[$sEventClassName][$sObserverIndex]->PkgCmsEventNotify($oEvent);
            }
        }

        return $oEvent;
    }

    /**
     * @return string
     */
    private function GetFullEventNameFromEvent(IPkgCmsEvent $oEvent)
    {
        return $this->GetFullEventName($oEvent->GetContext(), $oEvent->GetName());
    }

    /**
     * @param string $sEventContext
     * @param string $sEventName
     *
     * @return string
     */
    private function GetFullEventName($sEventContext, $sEventName)
    {
        return $sEventContext.'::'.$sEventName;
    }
}
