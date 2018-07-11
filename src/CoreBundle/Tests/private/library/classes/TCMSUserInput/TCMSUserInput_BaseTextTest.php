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

class TCMSUserInput_BaseTextTest extends TestCase
{
    /**
     * @var TCMSUserInput_BaseText
     */
    private $subject;
    /**
     * @var array|string
     */
    private $actualResult;

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->subject = null;
        $this->actualResult = null;
    }

    /**
     * @dataProvider getDataForTestFilter
     *
     * @param array|string $value
     * @param array|string $expectedResult
     */
    public function testFilter($value, $expectedResult)
    {
        $this->givenATCMSUserInput_BaseText();
        $this->whenFilterIsCalled($value);
        $this->thenTheExpectedResultShouldBeReturned($expectedResult);
    }

    /**
     * @return array
     */
    public function getDataForTestFilter()
    {
        return [
            ['', ''],
            [' ', ' '],
            ['test', 'test'],
            ['test<foo>', 'test<foo>'],
            ["test\0", 'test'],
            ["test\e", 'test'],
            ["\e", ''],
            ["\x1f", ''],
            ["test\n", "test\n"],
            ["test\n\t\rfoo", "test\n\t\rfoo"],
            ['[{_SLASH-N_}]', '[{_SLASH-N_}]'],
            [["fo\no", "bar\0"], ["fo\no", 'bar']],
        ];
    }

    private function givenATCMSUserInput_BaseText()
    {
        $this->subject = new TCMSUserInput_BaseText();
    }

    /**
     * @param array|string $value
     */
    private function whenFilterIsCalled($value)
    {
        $this->actualResult = $this->subject->Filter($value);
    }

    /**
     * @param array|string $expectedResult
     */
    private function thenTheExpectedResultShouldBeReturned($expectedResult)
    {
        $this->assertEquals($expectedResult, $this->actualResult);
    }
}
