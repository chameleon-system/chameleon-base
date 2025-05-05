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

class ViewMapperConfigTest extends TestCase
{
    public function testGetConfigs()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne,MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals(['one', 'two'], $config->getConfigs());
    }

    public function testGetMappers()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne,MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals(['MapperOne', 'MapperTwo'], $config->getMappersForConfig('one'));
    }

    public function testGetMappersWithAdditionalConfig()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne[foo->bar][baz->chucknorris],MapperTwo{aFoo}[foo->bar][baz->chucknorris]\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals(['MapperOne', 'MapperTwo'], $config->getMappersForConfig('one'));
    }

    public function testGetSnippet()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne,MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals('foobar.html.twig', $config->getSnippetForConfig('one'));
    }

    public function testGetTransformationsForMapper()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne[foo->bar][baz->chucknorris],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals(['foo' => 'bar', 'baz' => 'chucknorris'], $config->getTransformationsForMapper('one', 'MapperOne'));
        $this->assertEquals([], $config->getTransformationsForMapper('one', 'MapperTwo'));
    }

    public function testGetArrayMappingForMapper()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals('arraymapping', $config->getArrayMappingForMapper('one', 'MapperOne'));
    }

    public function testGetConfigCount()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");
        $this->assertEquals(2, $config->getConfigCount());
    }

    public function testGetZeroConfigCount()
    {
        $config = new ViewMapperConfig('');
        $this->assertEquals(0, $config->getConfigCount());
    }

    public function testGetParsedConfig()
    {
        $config = new ViewMapperConfig("one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo");

        $expected = [
            'one' => [
                'snippet' => 'foobar.html.twig',
                'aMapper' => [
                    0 => [
                            'arrayMapping' => 'arraymapping',
                            'varMapping' => [
                                'foo' => 'bar',
                                'baz' => 'chucknorris',
                            ],
                            'name' => 'MapperOne',
                    ],
                    1 => [
                            'arrayMapping' => null,
                            'varMapping' => [],
                            'name' => 'MapperTwo',
                        ],
                    ],
            ],
            'two' => [
                'snippet' => 'foobaz.html.twig',
                'aMapper' => [
                    0 => [
                        'arrayMapping' => null,
                        'varMapping' => [],
                        'name' => 'MapperOne',
                    ],
                    1 => [
                        'arrayMapping' => null,
                        'varMapping' => [],
                        'name' => 'MapperTwo',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $config->getPlainParsedConfig());
    }

    public function testGetAsString()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo\nthree=\nfour";
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo\nthree=\nfour";
        $config = new ViewMapperConfig($configString);

        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testAddMapperString()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $config = new ViewMapperConfig($configString);
        $config->addMapper('two', 'A-NEW-MAPPER');
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo,A-NEW-MAPPER";
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testAddMapperStringAfterMapperX()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $config = new ViewMapperConfig($configString);
        $config->addMapper('two', 'A-NEW-MAPPER', 'MapperOne');
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,A-NEW-MAPPER,MapperTwo";
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testAddMapperArray()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $config = new ViewMapperConfig($configString);
        $config->addMapper('two',
            [
                'arrayMapping' => null,
                'varMapping' => [],
                'name' => 'A-NEW-MAPPER',
            ]
        );
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo,A-NEW-MAPPER";
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testRemoveMapper()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperTwo";
        $config = new ViewMapperConfig($configString);
        $config->removeMapper('two', 'MapperOne');
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testReplaceMapperForAllConfigs()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo\nthree=barbaz.html.twig\nfour=\nfive";
        $expected = "one=foobar.html.twig;MapperNineThousand{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperNineThousand,MapperTwo\nthree=barbaz.html.twig\nfour=\nfive";
        $config = new ViewMapperConfig($configString);
        $config->replaceMapper('MapperOne', 'MapperNineThousand');
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testReplaceMapperForSingleConfig()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $expected = "one=foobar.html.twig;MapperNineThousand{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $config = new ViewMapperConfig($configString);
        $config->replaceMapper('MapperOne', 'MapperNineThousand', 'one');
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testChangeSnippet()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz2.html.twig;MapperOne,MapperTwo";
        $config = new ViewMapperConfig($configString);
        $config->changeSnippet('two', 'foobaz2.html.twig');
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testAddConfig()
    {
        $configString = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1[foo->bar]{arraymapping}[baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo";
        $expected = "one=foobar.html.twig;MapperOne{arraymapping}[foo->bar][baz->chucknorris],MapperOne1{arraymapping}[foo->bar][baz->chucknorris],MapperOne2{arraymapping},MapperOne3[foo->bar],MapperTwo\ntwo=foobaz.html.twig;MapperOne,MapperTwo\nthree=three.html.twig;mapper1,mapper2";
        $config = new ViewMapperConfig($configString);
        $config->addConfig('three', 'three.html.twig',
            ['mapper1', 'mapper2']
        );
        $string = $config->getAsString();
        $this->assertEquals($expected, $string);
    }

    public function testVisitorTransformation()
    {
        $visitor = new MapperVisitor();
        $visitor->setTransformations(['foo' => 'bar']);
        $visitor->SetMappedValue('foo', 'baz');
        $expected = [
            'bar' => 'baz',
        ];
        $this->assertEquals($expected, $visitor->GetMappedValues());
    }

    public function testVisitorTransformationFromArray()
    {
        $visitor = new MapperVisitor();
        $visitor->setTransformations(['foo' => 'bar']);
        $visitor->SetMappedValueFromArray(['foo' => 'baz']);
        $expected = [
            'bar' => 'baz',
        ];
        $this->assertEquals($expected, $visitor->GetMappedValues());
    }

    public function testVisitorMapToArray()
    {
        $visitor = new MapperVisitor();
        $visitor->setMapToArray('aFoo');
        $visitor->SetMappedValue('foo', 'baz');
        $expected = [
            'aFoo' => [
                'foo' => 'baz',
            ],
        ];
        $this->assertEquals($expected, $visitor->GetMappedValues());
    }

    public function testVisitorMapToArrayAndTransform()
    {
        $visitor = new MapperVisitor();
        $visitor->setMapToArray('aFoo');
        $visitor->setTransformations(['foo' => 'bar']);
        $visitor->SetMappedValue('foo', 'baz');
        $expected = [
            'aFoo' => [
                'bar' => 'baz',
            ],
        ];
        $this->assertEquals($expected, $visitor->GetMappedValues());
    }
}
