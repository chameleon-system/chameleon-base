<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

class MapperCacheTriggerRestrictedProxyTest extends TestCase
{
    public function testAddTrigger()
    {
        $oTriggerManager = new MapperCacheTrigger();
        $oProxy = new MapperCacheTriggerRestrictedProxy($oTriggerManager);
        $oProxy->addTrigger('table', 'id');
        $this->assertEquals([['table' => 'table', 'id' => 'id']], $oTriggerManager->getTrigger());
    }
}
