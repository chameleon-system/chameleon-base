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

class MapperPostRenderVisitorProxyTest extends TestCase
{
    public function testGetSetSourceObject()
    {
        $oRequirement = new MapperRequirements();
        $oRequirement->NeedsSourceObject('bar', 'string');
        $aSource = ['bar' => 'foo'];
        $oVisitor = new MapperVisitor($aSource);
        $oVisitor->SetCurrentRequirements($oRequirement);
        $oRestrictedProxy = new MapperPostRenderVisitorProxy($oVisitor);
        $this->assertEquals('foo', $oRestrictedProxy->GetSourceObject('bar'));
    }

    public function testGetNotSetSourceObject()
    {
        $oRequirement = new MapperRequirements();
        $oRequirement->NeedsSourceObject('bar', 'string');
        $aSource = ['bar' => 'foo'];
        $oVisitor = new MapperVisitor($aSource);
        $oVisitor->SetCurrentRequirements($oRequirement);
        $oRestrictedProxy = new MapperPostRenderVisitorProxy($oVisitor);
        $oRestrictedProxy->GetSourceObject('bar2');
        $this->assertTrue($oRestrictedProxy->isVirtualSourceObject('bar2'));
    }

    public function testSetMappedValue()
    {
        $aSource = ['bar' => 'foo'];
        $oVisitor = new MapperVisitor($aSource);
        $oRestrictedProxy = new MapperPostRenderVisitorProxy($oVisitor);
        $oRestrictedProxy->SetMappedValue('bar2', 'foo2');
        $this->assertEquals(['bar2' => 'foo2'], $oVisitor->GetMappedValues());
    }
}
