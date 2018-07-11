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
 * do no extend from this class... instead extend from TTPkgExternalTrackerState.
/**/
class TPkgExternalTrackerStateEndPoint implements IPkgCmsSessionPostWakeupListener
{
    protected $aEventData = array();
    protected $aStateData = array();

    const EVENT_EXTRANET_LOGIN = 'pkgExtranet__Login';
    const EVENT_EXTRANET_REGISTRATION = 'pkgExtranet__Registration';
    const EVENT_CONTACT_FORM_SUBMIT = 'contact_form__Submit';

    private $dataProcessed = false;
    /**
     * property is used to store event data in the serialized version of the object (we need to delay wakeup until the session is loaded when the object is loaded via session).
     *
     * @var string
     */
    protected $rawEventData = null;

    public function __sleep()
    {
        if (true === $this->dataProcessed) {
            $this->Clear();
        }

        return array('aEventData');
    }

    public function AddStateData($sKey, $oObject)
    {
        $this->aStateData[$sKey] = $oObject;
    }

    public function GetStateData($sKey)
    {
        if (array_key_exists($sKey, $this->aStateData)) {
            return $this->aStateData[$sKey];
        } else {
            return false;
        }
    }

    public function SetActivePage(TdbCmsTplPage $oActivePage)
    {
        $this->AddStateData('__oActivePage', $oActivePage);
    }

    /**
     * @return TCMSActivePage
     */
    public function GetActivePage()
    {
        return $this->GetStateData('__oActivePage');
    }

    public function AddEventData($sEventName, $aParameter)
    {
        $this->aEventData[$sEventName] = $aParameter;
    }

    public function GetEventData($sEventName)
    {
        if ($this->HasEvent($sEventName)) {
            return $this->aEventData[$sEventName];
        } else {
            return false;
        }
    }

    /**
     * return true if the event was triggered.
     *
     * @param $sEventName
     *
     * @return bool
     */
    public function HasEvent($sEventName)
    {
        return array_key_exists($sEventName, $this->aEventData);
    }

    public function GetEventList()
    {
        return $this->aEventData;
    }

    /**
     * clear the state data.
     */
    public function Clear()
    {
        $this->aEventData = array();
        $this->aStateData = array();
    }

    /**
     * @param bool $dataProcessed
     */
    public function setDataProcessed($dataProcessed)
    {
        $this->dataProcessed = $dataProcessed;
    }

    /**
     * method is called on objects recovered from session after the session has been started.
     * you can use this method to perform post session wakeup logic - but do not use this to delay property serialization of an object (see #25251 to see why this is a bad idea).
     */
    public function sessionWakeupHook()
    {
        $sessionWakeUp = new TPkgCmsSessionWakeUpService();
        $sessionWakeUp->wakeUpSessionData($this->aEventData);
        $sessionWakeUp->wakeUpSessionData($this->aStateData);
    }
}
