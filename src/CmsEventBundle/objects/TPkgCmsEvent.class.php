<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsEvent implements IPkgCmsEvent
{
    private $sContext = null;
    private $sName = null;
    private $aData = array();
    private $oSubject = null;

    /**
     * @return object
     */
    public function GetSubject()
    {
        return $this->oSubject;
    }

    /**
     * @param object $oSubject -the object that created the event
     *
     * @return $this
     */
    public function SetSubject($oSubject)
    {
        $this->oSubject = $oSubject;

        return $this;
    }

    public function GetContext()
    {
        return $this->sContext;
    }

    public function GetName()
    {
        return $this->sName;
    }

    public function GetData()
    {
        return $this->aData;
    }

    /**
     * @param $sContext
     *
     * @return $this
     */
    public function SetContext($sContext)
    {
        $this->sContext = $sContext;

        return $this;
    }

    /**
     * @param $sName
     *
     * @return $this
     */
    public function SetName($sName)
    {
        $this->sName = $sName;

        return $this;
    }

    /**
     * @param $aData
     *
     * @return $this
     */
    public function SetData($aData)
    {
        $this->aData = $aData;

        return $this;
    }

    public static function &GetNewInstance($oSubject, $sContext, $sName, $aData = array())
    {
        $sCallingClass = get_called_class();
        /** @var $oInstance TPkgCmsEvent */
        $oInstance = new $sCallingClass();
        $oInstance->SetSubject($oSubject);
        $oInstance->SetContext($sContext);
        $oInstance->SetName($sName);
        $oInstance->SetData($aData);

        return $oInstance;
    }
}
