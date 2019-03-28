<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IMapperCacheTriggerRestricted
{
    /**
     * @param string            $sTable
     * @param string|array|null $sId
     */
    public function addTrigger($sTable, $sId = null);
}
