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
 * /**/
class TPkgExternalTrackerStateEndPoint implements IPkgCmsSessionPostWakeupListener
{
    /**
     * @var array<string, mixed>
     */
    protected $aEventData = [];

    /**
     * @var array<string, TdbCmsTplPage>
     */
    protected $aStateData = [];

    public const EVENT_EXTRANET_LOGIN = 'pkgExtranet__Login';
    public const EVENT_EXTRANET_REGISTRATION = 'pkgExtranet__Registration';
    public const EVENT_CONTACT_FORM_SUBMIT = 'contact_form__Submit';

    /**
     * @var bool
     */
    private $dataProcessed = false;
    /**
     * property is used to store event data in the serialized version of the object (we need to delay wakeup until the session is loaded when the object is loaded via session).
     *
     * @var string
     */
    protected $rawEventData;

    public function __sleep()
    {
        if (true === $this->dataProcessed) {
            $this->Clear();
        }

        return ['aEventData'];
    }

    /**
     * @param string $sKey
     * @param TdbCmsTplPage $oObject
     *
     * @return void
     */
    public function AddStateData($sKey, $oObject)
    {
        $this->aStateData[$sKey] = $oObject;
    }

    /**
     * @param string $sKey
     *
     * @return TdbCmsTplPage|false
     */
    public function GetStateData($sKey)
    {
        if (array_key_exists($sKey, $this->aStateData)) {
            return $this->aStateData[$sKey];
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function SetActivePage(TdbCmsTplPage $oActivePage)
    {
        $this->AddStateData('__oActivePage', $oActivePage);
    }

    /**
     * @return TCMSActivePage
     *
     * @psalm-suppress FalsableReturnStatement
     */
    public function GetActivePage()
    {
        return $this->GetStateData('__oActivePage');
    }

    /**
     * @param string $sEventName
     *
     * @return void
     */
    public function AddEventData($sEventName, $aParameter)
    {
        $this->aEventData[$sEventName] = $aParameter;
    }

    /**
     * @param string $sEventName
     *
     * @return mixed|false
     */
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
     * @param string $sEventName
     *
     * @return bool
     */
    public function HasEvent($sEventName)
    {
        return array_key_exists($sEventName, $this->aEventData);
    }

    /**
     * @return array
     *
     * @psalm-return array<string, mixed>
     */
    public function GetEventList()
    {
        return $this->aEventData;
    }

    /**
     * clear the state data.
     *
     * @return void
     */
    public function Clear()
    {
        $this->aEventData = [];
        $this->aStateData = [];
    }

    /**
     * @param bool $dataProcessed
     *
     * @return void
     */
    public function setDataProcessed($dataProcessed)
    {
        $this->dataProcessed = $dataProcessed;
    }

    /**
     * method is called on objects recovered from session after the session has been started.
     * you can use this method to perform post session wakeup logic - but do not use this to delay property serialization of an object (see #25251 to see why this is a bad idea).
     *
     * @return void
     */
    public function sessionWakeupHook()
    {
        $sessionWakeUp = new TPkgCmsSessionWakeUpService();
        $sessionWakeUp->wakeUpSessionData($this->aEventData);
        $sessionWakeUp->wakeUpSessionData($this->aStateData);
    }
}
