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
 * @deprecated since 8.0 - use the symfony event dispatcher instead
 */
class TPkgCmsEvent implements IPkgCmsEvent
{
    /**
     * @var string|null
     */
    private $sContext;

    /**
     * @var string|null
     */
    private $sName;

    /**
     * @var array<string, mixed>
     */
    private $aData = [];

    /**
     * @var object|null
     */
    private $oSubject;

    /**
     * @return object|null
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

    /**
     * @return string|null
     */
    public function GetContext()
    {
        return $this->sContext;
    }

    /**
     * @return string|null
     */
    public function GetName()
    {
        return $this->sName;
    }

    /**
     * @return array
     */
    public function GetData()
    {
        return $this->aData;
    }

    /**
     * @param string $sContext
     *
     * @return $this
     */
    public function SetContext($sContext)
    {
        $this->sContext = $sContext;

        return $this;
    }

    /**
     * @param string $sName
     *
     * @return $this
     */
    public function SetName($sName)
    {
        $this->sName = $sName;

        return $this;
    }

    /**
     * @param array<string, mixed> $aData
     *
     * @return $this
     */
    public function SetData($aData)
    {
        $this->aData = $aData;

        return $this;
    }

    /**
     * @param string $sContext
     * @param string $sName
     * @param object $oSubject
     * @param array<string, mixed> $aData
     *
     * @return static
     */
    public static function GetNewInstance($oSubject, $sContext, $sName, $aData = [])
    {
        $sCallingClass = get_called_class();

        /** @var static $oInstance */
        $oInstance = new $sCallingClass();
        $oInstance->SetSubject($oSubject);
        $oInstance->SetContext($sContext);
        $oInstance->SetName($sName);
        $oInstance->SetData($aData);

        return $oInstance;
    }
}
