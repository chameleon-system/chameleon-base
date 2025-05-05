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

class MapperCacheTriggerTest extends TestCase
{
    public function testSetAndGetTrigger()
    {
        $oTrigger = new MapperCacheTrigger();
        $oTrigger->addTrigger('table', 'id');

        $aTrigger = $oTrigger->getTrigger();

        $this->assertEquals([['table' => 'table', 'id' => 'id']], $aTrigger);
    }

    public function testGetTriggerBeforeSet()
    {
        $oTrigger = new MapperCacheTrigger();

        $aTrigger = $oTrigger->getTrigger();

        $this->assertEquals(null, $aTrigger);
    }
}
