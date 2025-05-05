<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\ModuleMapperChainConfigTest;

use PHPUnit\Framework\TestCase;

class ModuleMapperChainConfigTest extends TestCase
{
    /**
     * @var \ModuleMapperChainConfig
     */
    private $mapperChainConfig;
    private $exceptionThrown;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->mapperChainConfig = null;
        $this->exceptionThrown = null;
    }

    /**
     * @test
     *
     * @dataProvider dataProviderConfigStrings
     */
    public function itIsLoadedFromString($input, $expectedConfig)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($input);
        $this->then_we_expect_that_getMapperChains_returns($expectedConfig);
    }

    public function dataProviderConfigStrings()
    {
        return [
            [
                <<<'EOD'
                alias=myMapper
EOD
            , ['alias' => ['myMapper']],
            ],
            [
                <<<'EOD'
                alias = myMapper
EOD
            , ['alias' => ['myMapper']],
            ],
            [
                <<<'EOD'
                alias= myMapper
EOD
            , ['alias' => ['myMapper']],
            ],
            [
                <<<'EOD'
                alias =myMapper
EOD
            , ['alias' => ['myMapper']],
            ],
            [
                <<<'EOD'
                alias =myMapper,
EOD
            , ['alias' => ['myMapper']],
            ],
            [
                <<<'EOD'
                alias =myMapper,myMapper2
EOD
            , ['alias' => ['myMapper', 'myMapper2']],
            ],
            [
                <<<'EOD'
                alias =myMapper,myMapper2,
EOD
            , ['alias' => ['myMapper', 'myMapper2']],
            ],
            [
                <<<'EOD'
                alias =myMapper,myMapper2,
                alias2 = newMapper,foo\bar\mapper
EOD
            , ['alias' => ['myMapper', 'myMapper2'], 'alias2' => ['newMapper', 'foo\bar\mapper']],
            ],
        ];
    }

    private function given_a_mapping_chain_config_instance()
    {
        $this->mapperChainConfig = new \ModuleMapperChainConfig();
    }

    private function when_we_call_loadFromString_with($input)
    {
        $this->mapperChainConfig->loadFromString($input);
    }

    private function then_we_expect_that_getMapperChains_returns($expectedConfig)
    {
        $this->assertEquals($expectedConfig, $this->mapperChainConfig->getMapperChains());
    }

    /**
     * @test
     *
     * @dataProvider dataProviderConfigStringToConfigString
     */
    public function itConvertsConfigurationToString($input, $expectedConfig)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($input);
        $this->then_we_expect_that_getAsString_returns($expectedConfig);
    }

    public function dataProviderConfigStringToConfigString()
    {
        return [
            ['alias = foo', 'alias = foo'],
            ['alias = foo,', 'alias = foo'],
            ['alias = foo,bar', 'alias = foo, bar'],
            ['alias = foo,bar,\\foo\\bar\\mapper', 'alias = foo, bar, \\foo\\bar\\mapper'],
            ["alias = foo,bar,\\foo\\bar\\mapper\nalias2 = mapper2", "alias = foo, bar, \\foo\\bar\\mapper\nalias2 = mapper2"],
        ];
    }

    private function then_we_expect_that_getAsString_returns($input)
    {
        $this->assertEquals($input, $this->mapperChainConfig->getAsString());
    }

    /**
     * @test
     *
     * @dataProvider dataProviderAddMapperToChain
     */
    public function addMapperToChain($initialStateAsString, $expectedState, $mapperChainName, $newMapper, $positionAfter)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($initialStateAsString);
        $this->when_we_call_addMapperToChain_with($mapperChainName, $newMapper, $positionAfter);
        $this->then_we_expect_that_getAsString_returns($expectedState);
    }

    public function dataProviderAddMapperToChain()
    {
        return [
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, newMapper, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $mapperChainName
                'newMapper', // $newMapper
                'myMapper1', // $positionAfter
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3, newMapper\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $mapperChainName
                'newMapper', // $newMapper
                null, // $positionAfter
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b, newMapper", // $expectedState
                'someAlias2', // $mapperChainName
                'newMapper', // $newMapper
                'myMapper3b', // $positionAfter
            ],
        ];
    }

    private function when_we_call_addMapperToChain_with($mapperChainName, $newMapper, $positionAfter)
    {
        $this->mapperChainConfig->addMapperToChain($mapperChainName, $newMapper, $positionAfter);
    }

    /**
     * @test
     *
     * @dataProvider dataProviderRemoveMapperFromMapperChain
     */
    public function idShouldBeAbleToRemoveAMapperFromAMapperChain($initialStateAsString, $expectedState, $mapperChainName, $mapperName, $expectedExceptionType)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($initialStateAsString);
        $this->when_we_call_removeMapperFromMapperChain_with($mapperChainName, $mapperName);
        $this->then_we_expect_that_getAsString_returns($expectedState);
        $this->then_we_expect_to_get_an_exception_of_type($expectedExceptionType);
    }

    public function dataProviderRemoveMapperFromMapperChain()
    {
        return [
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $mapperChainName
                'myMapper1', // $mapperName
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $mapperChainName
                'myMapper2', // $mapperName
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $mapperChainName
                'myMapper3', // $mapperName
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b", // $expectedState
                'someAlias2', // $mapperChainName
                'myMapper3b', // $mapperName
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias3', // $mapperChainName
                'myMapper3b', // $mapperName
                '\ErrorException',
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $mapperChainName
                'myMapper3b', // $mapperName
                '\ErrorException',
            ],
        ];
    }

    private function when_we_call_removeMapperFromMapperChain_with($mapperChainName, $mapperName)
    {
        try {
            $this->mapperChainConfig->removeMapperFromMapperChain($mapperChainName, $mapperName);
        } catch (\ErrorException $e) {
            $this->exceptionThrown = $e;
        }
    }

    private function then_we_expect_to_get_an_exception_of_type($expectedExceptionType)
    {
        if (null === $expectedExceptionType) {
            $this->assertNull($this->exceptionThrown, 'there was an exception, but there should not have been one. Exception thrown: '.(string) $this->exceptionThrown);
        } else {
            $this->assertInstanceOf($expectedExceptionType, $this->exceptionThrown, 'we expected an excetion of type {$expectedExceptionType} - but got '.(string) $this->exceptionThrown);
        }
    }

    /**
     * @test
     *
     * @dataProvider dataProviderRemoveMapperChain
     */
    public function itShouldBeAbleToRemoveAMapperChain($initialStateAsString, $expectedState, $mapperChainName, $expectedExceptionType)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($initialStateAsString);
        $this->when_we_call_removeMapperChain_with($mapperChainName);
        $this->then_we_expect_that_getAsString_returns($expectedState);
        $this->then_we_expect_to_get_an_exception_of_type($expectedExceptionType);
    }

    public function dataProviderRemoveMapperChain()
    {
        return [
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                'someAlias2 = myMapper1b, myMapper2b, myMapper3b', // $expectedState
                'someAlias', // $mapperChainName
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                'someAlias = myMapper1, myMapper2, myMapper3', // $expectedState
                'someAlias2', // $mapperChainName
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias3', // $mapperChainName
                '\ErrorException',
            ],
        ];
    }

    private function when_we_call_removeMapperChain_with($mapperChainName)
    {
        try {
            $this->mapperChainConfig->removeMapperChain($mapperChainName);
        } catch (\ErrorException $e) {
            $this->exceptionThrown = $e;
        }
    }

    /**
     * @test
     *
     * @dataProvider dataProviderAddMapperChain
     */
    public function itShouldAddANewMapperChain($initialStateAsString, $expectedState, $newMapperChainName, $newMapperChainList, $expectedExceptionType)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($initialStateAsString);
        $this->when_we_call_addMapperChain_with($newMapperChainName, $newMapperChainList);
        $this->then_we_expect_that_getAsString_returns($expectedState);
        $this->then_we_expect_to_get_an_exception_of_type($expectedExceptionType);
    }

    public function dataProviderAddMapperChain()
    {
        return [
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b\nnewAlias = mapper1, mapper2, \\mapper\\three", // $expectedState
                'newAlias', // $newMapperChainName
                ['mapper1', 'mapper2', '\\mapper\\three'], // $newMapperChainList
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b\nnewAlias = mapper1", // $expectedState
                'newAlias', // $newMapperChainName
                ['mapper1'], // $newMapperChainList
                null,
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'newAlias', // $newMapperChainName
                [], // $newMapperChainList
                '\ErrorException',
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'someAlias', // $newMapperChainName
                ['foo', 'bar'], // $newMapperChainList
                '\ErrorException',
            ],
        ];
    }

    private function when_we_call_addMapperChain_with($newMapperChainName, $newMapperChainList)
    {
        try {
            $this->mapperChainConfig->addMapperChain($newMapperChainName, $newMapperChainList);
        } catch (\ErrorException $e) {
            $this->exceptionThrown = $e;
        }
    }

    /**
     * @test
     *
     * @dataProvider dataProviderReplaceMapper
     *
     * @param string $initialStateAsString
     * @param string $expectedState
     * @param string $oldMapperName
     * @param string $newMapperName
     * @param string|null $mapperChainName
     * @param string|null $expectedExceptionType
     */
    public function itShouldReplaceMappers($initialStateAsString, $expectedState, $oldMapperName, $newMapperName, $mapperChainName, $expectedExceptionType)
    {
        $this->given_a_mapping_chain_config_instance();
        $this->when_we_call_loadFromString_with($initialStateAsString);
        $this->when_we_call_replaceMapper_with($oldMapperName, $newMapperName, $mapperChainName);
        $this->then_we_expect_that_getAsString_returns($expectedState);
        $this->then_we_expect_to_get_an_exception_of_type($expectedExceptionType);
    }

    /**
     * @param string $oldMapperName
     * @param string $newMapperName
     * @param string|null $mapperChainName
     */
    private function when_we_call_replaceMapper_with($oldMapperName, $newMapperName, $mapperChainName)
    {
        try {
            $this->mapperChainConfig->replaceMapper($oldMapperName, $newMapperName, $mapperChainName);
        } catch (\ErrorException $e) {
            $this->exceptionThrown = $e;
        }
    }

    /**
     * @return array
     */
    public function dataProviderReplaceMapper()
    {
        return [
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper9000, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'myMapper2', // $oldMapperName
                'myMapper9000', // $newMapperName
                null, // $mapperChainName
                null, // expectedExceptionType
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper9000, myMapper3\nsomeAlias2 = myMapper1b, myMapper9000, myMapper3b", // $expectedState
                'myMapper2', // $oldMapperName
                'myMapper9000', // $newMapperName
                null, // $mapperChainName
                null, // expectedExceptionType
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper9000, myMapper3\nsomeAlias2 = myMapper1b, myMapper2, myMapper3b", // $expectedState
                'myMapper2', // $oldMapperName
                'myMapper9000', // $newMapperName
                'someAlias', // $mapperChainName
                null, // expectedExceptionType
            ],
            [
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $initialStateAsString
                "someAlias = myMapper1, myMapper2, myMapper3\nsomeAlias2 = myMapper1b, myMapper2b, myMapper3b", // $expectedState
                'myMapper2', // $oldMapperName
                'myMapper9000', // $newMapperName
                'nonExistingAlias', // $mapperChainName
                '\ErrorException', // expectedExceptionType
            ],
        ];
    }
}
