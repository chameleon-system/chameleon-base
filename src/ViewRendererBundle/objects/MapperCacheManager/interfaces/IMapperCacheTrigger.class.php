<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IMapperCacheTrigger extends IMapperCacheTriggerRestricted
{
    /**
     * @return array{table: string, id: string}[]|null in the form array(array('table'=>tablename,'id'=>id),...)
     */
    public function getTrigger();
}
