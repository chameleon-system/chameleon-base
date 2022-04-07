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
    /**
     * @var null|string
     * @psalm-var null|IPkgCmsEvent::CONTEXT_*|TPkgCmsEvent::CONTEXT_*
     */
    private $sContext = null;

    /**
     * @var null|string
     * @psalm-var null|IPkgCmsEvent::NAME_*|TPkgCmsEvent::NAME_*
     */
    private $sName = null;

    /**
     * @var array
     */
    private $aData = array();

    /**
     * @var null|object
     */
    private $oSubject = null;

    /**
     * @return null|object
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
     * @return null|string
     *
     * @psalm-return IPkgCmsEvent::CONTEXT_*|TPkgCmsEvent::CONTEXT_*|null
     */
    public function GetContext()
    {
        return $this->sContext;
    }

    /**
     * @return null|string
     *
     * @psalm-return IPkgCmsEvent::NAME_*|TPkgCmsEvent::NAME_*|null
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
     * @psalm-param IPkgCmsEvent::CONTEXT_*|TPkgCmsEvent::CONTEXT_* $sContext
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
     * @psalm-param IPkgCmsEvent::NAME_*|TPkgCmsEvent::NAME_* $sName
     *
     * @return $this
     */
    public function SetName($sName)
    {
        $this->sName = $sName;

        return $this;
    }

    /**
     * @param array $aData
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
     * @param \ChameleonSystem\CoreBundle\Controller\ChameleonController $oSubject
     * @param string[] $aData
     *
     * @psalm-param IPkgCmsEvent::NAME_*|TPkgCmsEvent::NAME_* $sName
     * @psalm-param IPkgCmsEvent::CONTEXT_*|TPkgCmsEvent::CONTEXT_* $sContext
     */
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
