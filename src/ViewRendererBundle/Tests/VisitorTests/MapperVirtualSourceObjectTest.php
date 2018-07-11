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

class MapperVirtualSourceObjectTest extends TestCase
{
    public function testGetString()
    {
        $oVirt = new MapperVirtualSourceObject();
        $sString = (string) $oVirt;

        $this->assertEquals('[null]', $sString);
    }

    public function testGetObject()
    {
        $oVirt = new MapperVirtualSourceObject();
        $sString = (string) $oVirt->someProperty;

        $this->assertEquals('[null]', $sString);
    }

    public function testCallMethod()
    {
        $oVirt = new MapperVirtualSourceObject();
        $sString = (string) $oVirt->GetSomeObject()->AndOneMore()->fieldSomeString;

        $this->assertEquals('[null]', $sString);
    }
}
