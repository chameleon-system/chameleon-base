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
 * defines an event. every event is identified by the context and the event name
 * context: describes what package or subpackage the event belongs to. this helps
 *          to isolate events within a package from other events
 * name: with in each context the events are identified by their name.
 *
 * events may hold data specific to the event. this data can be accessed via GetData
/**/
interface IPkgCmsEvent
{
    const CONTEXT_CORE = 'core';
    const CONTEXT_CUSTOM_CORE = 'custom-core';
    const CONTEXT_CUSTOMER = 'customer';

    const NAME_PRE_OUTPUT_CALLBACK_FUNCTION = 'PreOutputCallbackFunction';
    const NAME_GET_CUSTOM_FOOTER_DATA = 'getCustomFooterData';
    const NAME_GET_CUSTOM_HEADER_DATA = 'getCustomHeaderData';

    /**
     * @abstract
     *
     * @return object
     */
    public function GetSubject();

    /**
     * @abstract
     *
     * @param object $oSubject -the object that created the event
     *
     * @return mixed
     */
    public function SetSubject($oSubject);

    /**
     * @abstract
     * returns the context of the event. Context describes what package or subpackage the event belongs to. this helps
     * to isolate events within a package from other events
     *
     * @return null|string
     *
     * @psalm-return IPkgCmsEvent::CONTEXT_*|TPkgCmsEvent::CONTEXT_*|null
     */
    public function GetContext();

    /**
     * @abstract
     * each event has, in addition to the context a name. the combination is used to identify the specific event
     *
     * @return null|string
     *
     * @psalm-return self::NAME_*|TPkgCmsEvent::NAME_*|null
     */
    public function GetName();

    /**
     * @abstract
     * returns data associated with the event
     *
     * @return array
     */
    public function GetData();

    /**
     * @abstract
     * sets the context of the event - use IPkgCmsEvent::CONTEXT_* or TPkgCmsEvent::CONTEXT_*
     *
     * @param string $sContext
     * @psalm-param self::CONTEXT_*|TPkgCmsEvent::CONTEXT_* $sContext
     *
     * @return $this
     */
    public function SetContext($sContext);

    /**
     * @abstract
     * sets the name of the event in the context set via SetContext. use IPkgCmsEvent::NAME_* or TPkgCmsEvent::NAME_*
     *
     * @param string $sName
     * @psalm-param self::NAME_*|TPkgCmsEvent::NAME_* $sName
     *
     * @return $this
     *
     */
    public function SetName($sName);

    /**
     * @abstract
     * set additional data for the event
     *
     * @param array $aData
     *
     * @return $this
     */
    public function SetData($aData);

    /**
     * @static
     *
     * @param object $oSubject - the object that created the event
     * @param string $sContext - use IPkgCmsEvent::CONTEXT_* or TPkgCmsEvent::CONTEXT_*
     * @param string $sName - use IPkgCmsEvent::NAME_* or TPkgCmsEvent::NAME_*
     * @param array $aData - any data you want to pass to the event
     *
     * @psalm-param IPkgCmsEvent::NAME_*|TPkgCmsEvent::NAME_* $sName
     * @psalm-param IPkgCmsEvent::CONTEXT_*|TPkgCmsEvent::CONTEXT_* $sContext
     *
     * @return IPkgCmsEvent
     *                      returns a new instance of an event for the defined context.
     *                      the returned instance will be of the type used to call the method. So calling a subclass will return an instance of this subclass
     */
    public static function &GetNewInstance($oSubject, $sContext, $sName, $aData = array());
}
