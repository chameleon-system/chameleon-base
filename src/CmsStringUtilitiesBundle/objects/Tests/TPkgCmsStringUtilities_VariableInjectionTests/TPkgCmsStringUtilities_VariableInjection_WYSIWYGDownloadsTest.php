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

class TPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloadsTest extends TestCase
{
    /**
     * @var TPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads
     */
    protected $oPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads;

    /** @var ReflectionMethod */
    protected $getMatches;

    public function setUp(): void
    {
        $this->oPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads = new TPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads();
        $this->getMatches = new ReflectionMethod('TPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads', 'getMatches');
        $this->getMatches->setAccessible(true);
    }

    public function TearDown(): void
    {
        $this->oPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads = null;
    }

    public function testDownloadVariables()
    {
        $aTests = [
            // link with icon and kb
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf,ico,kb}]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf,ico,kb}]'],
            // leading space in name
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl, Halloween2012_3.pdf,ico,kb }]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl, Halloween2012_3.pdf,ico,kb }]'],
            // spaces everywhere
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd, dl ,Halloween2012_3.pdf,ico, kb}]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd, dl ,Halloween2012_3.pdf,ico, kb}]'],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,  dl,Halloween2012_3.pdf, ico,kb}]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,  dl,Halloween2012_3.pdf, ico,kb}]'],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl  ,Halloween2012_3.pdf  ,  kb ,   ico  }]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl  ,Halloween2012_3.pdf  ,  kb ,   ico  }]'],
            // only ico
            '<p>[{ 21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd ,dl,Halloween2012_3.pdf,ico}]</p>' => ['[{ 21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd ,dl,Halloween2012_3.pdf,ico}]'],
            // only kb
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf,kb}]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf,kb}]'],
            // no ico and kb
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf}]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf}]'],
            // duplicate param
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl  ,Halloween2012_3.pdf  ,  kb ,   kb  }]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl  ,Halloween2012_3.pdf  ,  ico ,   ico  }]</p>' => [],
            // comma without param
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl  ,Halloween2012_3.pdf, }]</p>' => [],
            // missing name
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl}]</p>' => [],
            // old id
            '<p>[{213434,dl,Halloween2012_3.pdf}]</p>' => ['[{213434,dl,Halloween2012_3.pdf}]'],
            // wrong id - old id's are only numeric, new id's have to be 36 characters alphanumeric with dashes
            '<p>[{213434das,dl,Halloween2012_3.pdf}]</p>' => [],
            '<p>[{21343-4das,dl,Halloween2012_3.pdf}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd-dfgs,dl,Halloween2012_3.pdf}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bddf5gs,dl,Halloween2012_3.pdf}]</p>' => [],
            // wrong param
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Halloween2012_3.pdf,ico fof fsdf wfwe}]</p>' => [],
            // double comma
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,,dl,Halloween2012_3.pdf}]</p>' => [],
            // placeholder characters in placeholder
            '<p>[{21f7e2b8-3e90-be4a-[{bd14-30f2db3cb6bd,dl,Hall}]oween2012_3.pdf,ico,kb}]</p>' => [],
            // all kind of opening brackets and double brackets
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[{een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{[een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[[een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{{een2012_3.pdf,ico,kb}]</p>' => [],
            // all kind of closing brackets and double brackets
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee]n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee}n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee}]n2012_3.pdf,ico,kb}]</p>' => ['[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee}]'],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee]}n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee]]n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallowee}}n2012_3.pdf,ico,kb}]</p>' => [],
            // empty brackets in name
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[]een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{}een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{{}}een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[[]]een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[{}]een2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{[]}een2012_3.pdf,ico,kb}]</p>' => [],
            // broken empty brackets in name
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{{}e}n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[[]e]n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[{}e]n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{[]e}n2012_3.pdf,ico,kb}]</p>' => [],
            // brackets around comma
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd[,]dl,Halloween2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd{,}dl,Halloween2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd{{,}}dl,Halloween2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd[[,]]dl,Halloween2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd[{,}]dl,Halloween2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd{[,]}dl,Halloween2012_3.pdf,ico,kb}]</p>' => [],
            // brackets with content in name
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{{e}}en2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[[e]]en2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[{e}]en2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{[e]}en2012_3.pdf,ico,kb}]</p>' => [],
            // broken brackets with content in name
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{{e}e}n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[[e]e]n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow[{e}e]n2012_3.pdf,ico,kb}]</p>' => [],
            '<p>[{21f7e2b8-3e90-be4a-bd14-30f2db3cb6bd,dl,Hallow{[e]e}n2012_3.pdf,ico,kb}]</p>' => [],
        ];

        foreach ($aTests as $sTest => $aExpected) {
            $result = $this->getMatches->invoke($this->oPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads, $sTest);
            $this->assertEquals($aExpected, $result);
        }
    }
}
