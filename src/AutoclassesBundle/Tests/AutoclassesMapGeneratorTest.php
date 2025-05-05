<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Tests;

use ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGenerator;
use PHPUnit\Framework\TestCase;

class AutoclassesMapGeneratorTest extends TestCase
{
    /**
     * @var AutoclassesMapGenerator
     */
    private $mapGenerator;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mapGenerator = new AutoclassesMapGenerator();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->mapGenerator = null;
    }

    /**
     * @test
     */
    public function itShouldGenerateACompleteAutoclassesClassmap()
    {
        $cacheDir = __DIR__.'/cache/';

        $actual = $this->mapGenerator->generateAutoclassesMap($cacheDir);

        $expected = [];
        $expected['TestClass'] = 'TestType';

        $this->assertEquals($expected, $actual);
    }
}
