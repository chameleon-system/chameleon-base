<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperCacheTriggerRestrictedProxy implements IMapperCacheTriggerRestricted
{
    /**
     * @var IMapperCacheTrigger
     */
    private $oMapperCacheTrigger;

    public function __construct(IMapperCacheTrigger $oMapperCacheTrigger)
    {
        $this->oMapperCacheTrigger = $oMapperCacheTrigger;
    }

    /**
     * {@inheritdoc}
     */
    public function addTrigger($sTable, $sId = null)
    {
        $this->oMapperCacheTrigger->addTrigger($sTable, $sId);
    }
}
