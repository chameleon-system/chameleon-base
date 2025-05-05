<?php

namespace ChameleonSystem\CoreBundle\Tests\Util;

use ChameleonSystem\CoreBundle\Util\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    /**
     * @var Hash
     */
    private $subject;

    public function dataProviderHashData()
    {
        $simpleArray = [
            'input' => [
                'foo' => 'bar',
            ],
            'expectedOutput' => '',
        ];
        $simpleArray['expectedOutput'] = $this->createHash($simpleArray['input']);

        $nestedArray = [
            'input' => [
                'afoo' => ['bo' => 'ba', 'zap' => 'foo'],
                'foo' => ['bar' => 'foo2'],
            ],
            'expectedOutput' => '',
        ];
        $nestedArray['expectedOutput'] = $this->createHash($nestedArray['input']);

        $nestedArrayCheckSort = [
            'input' => [
                'foo' => ['bar' => 'foo2'],
                'afoo' => ['zap' => 'foo', 'bo' => 'ba'],
            ],
            'expectedOutput' => $nestedArray['expectedOutput'],
        ];

        $nonUtf8Char = [
            'input' => [
                'afoo' => ['bo' => 'ba', 'zap' => 'foo'],
                'foo' => ['bar' => urldecode('%F6')],
            ],
            'expectedOutput' => '',
        ];
        $nonUtf8Char['expectedOutput'] = $this->createHash($nonUtf8Char['input']);

        $nonUtf8CharNestedArrayCheckSort = [
            'input' => [
                'foo' => ['bar' => urldecode('%F6')],
                'afoo' => ['zap' => 'foo', 'bo' => 'ba'],
            ],
            'expectedOutput' => $nonUtf8Char['expectedOutput'],
        ];

        $nonUtf8CharWithObject = [
            'input' => [
                'foo' => ['bar' => urldecode('%F6'), 'obj' => new \stdClass()],
            ],
            'expectedOutput' => '',
        ];
        $nonUtf8CharWithObject['expectedOutput'] = $this->createHash($nonUtf8CharWithObject['input']);

        return [
            'simple-array' => $simpleArray,
            'nested-array' => $nestedArray,
            'nested-array-check-sort' => $nestedArrayCheckSort,
            'non-utf8-char' => $nonUtf8Char,
            'non-utf8-nested-array-check-sort' => $nonUtf8CharNestedArrayCheckSort,
            'non-utf8-char-with-object' => $nonUtf8CharWithObject,
            'object' => [
                'input' => new \stdClass(),
                'expectedOutput' => $this->createHash(new \stdClass()),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->subject = new Hash();
    }

    /**
     * @dataProvider dataProviderHashData
     *
     * @param string $expectedHash
     */
    public function testHash($input, $expectedHash)
    {
        $this->assertEquals($expectedHash, $this->subject->hash32($input));
    }

    /**
     * @return string
     */
    private function createHash($data)
    {
        $lines = explode("\n", var_export($data, true));
        sort($lines);

        return md5(implode("\n", $lines));
    }
}
