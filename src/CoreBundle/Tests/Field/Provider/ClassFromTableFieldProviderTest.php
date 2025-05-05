<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Field\Provider;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessClassFromTableFieldProviderInterface;
use ChameleonSystem\CoreBundle\Field\Provider\ClassFromTableFieldProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ClassFromTableFieldProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ClassFromTableFieldProvider
     */
    private $classFromTableFieldProvider;
    /**
     * @var DataAccessClassFromTableFieldProviderInterface|ObjectProphecy
     */
    private $classFromTableFieldProviderDataAccessMock;
    /**
     * @var string
     */
    private $actualResult;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->classFromTableFieldProvider = null;
        $this->classFromTableFieldProviderDataAccessMock = null;
        $this->actualResult = null;
    }

    /**
     * @dataProvider provideDataForTestGetFieldClassNameFromTableField
     */
    public function testGetFieldClassNameFromTableField(string $tableField, ?string $expectedClassName): void
    {
        $this->givenAClassFromTableFieldProvider();
        $this->whenGetFieldClassNameFromTableFieldIsCalled($tableField);
        $this->thenTheReturnedClassNameShouldMatch($expectedClassName);
    }

    public function provideDataForTestGetFieldClassNameFromTableField(): array
    {
        return [
            'validTableField' => [
                '`cms_config`.`name`',
                'TCMSFieldVarChar',
            ],
            'idTableFieldSimple' => [
                'id',
                null,
            ],
            'idTableField' => [
                '`table_test_label`.`id`',
                null,
            ],
        ];
    }

    /**
     * @dataProvider provideDataForTestGetFieldClassNameFromTableFieldInvalidArguments
     */
    public function testGetFieldClassNameFromTableFieldInvalidArguments(string $tableField): void
    {
        $this->givenAClassFromTableFieldProvider();
        $this->thenAnInvalidArgumentExpectionIsExpected();
        $this->whenGetFieldClassNameFromTableFieldIsCalled($tableField);
    }

    public function provideDataForTestGetFieldClassNameFromTableFieldInvalidArguments(): array
    {
        return [
            'validTableFieldMissing' => [
                '`cms_config`.`missing_field`',
            ],
            'validTableFieldClassEmpty' => [
                '`cms_config`.`empty_class`',
            ],
        ];
    }

    /**
     * @dataProvider getDataForTestGetDictionaryFromTableField
     */
    public function testGetDictionaryFromTableField(string $tableField, ?array $expectedDictionary): void
    {
        $this->givenAClassFromTableFieldProvider();
        $this->whenGetDictionaryFromTableFieldIsCalled($tableField);
        $this->thenTheReturnedDictionaryShouldMatch($expectedDictionary);
    }

    public function getDataForTestGetDictionaryFromTableField(): array
    {
        return [
            'validTableField' => [
                '`table_test_label`.`field_test_label`',
                ['tableName' => 'table_test_label', 'fieldName' => 'field_test_label'],
            ],
            'idTableFieldSimple' => [
                'id',
                null,
            ],
            'idTableField' => [
                '`table_test_label`.`id`',
                ['tableName' => 'table_test_label', 'fieldName' => 'id'],
            ],
            'validTableTableMisquoted' => [
                '`table_test_label.`field_test_label`',
                ['tableName' => 'table_test_label', 'fieldName' => 'field_test_label'],
            ],
            'validTableFieldMisquoted' => [
                '`table_test_label.field_test_label',
                ['tableName' => 'table_test_label', 'fieldName' => 'field_test_label'],
            ],
            'invalidTableFieldUnquoted' => [
                'table_test_label',
                null,
            ],
            'invalidTableFieldMisquoted' => [
                '`table_test_label',
                null,
            ],
            'invalidTableFieldIncomplete' => [
                '`table_test_label`',
                null,
            ],
            'invalidTableFieldEmpty' => [
                '',
                null,
            ],
            'invalidTableFieldStacked' => [
                '`table_test_label`.`field_test_label`.`subfield_test_label`',
                null,
            ],
            'invalidTableFieldStackedDottedOnly' => [
                '.....',
                null,
            ],
            'invalidTableFieldStackedUndotted' => [
                '`table_test_label``field_test_label`',
                null,
            ],
        ];
    }

    private function givenAClassFromTableFieldProvider(): void
    {
        $this->classFromTableFieldProviderDataAccessMock = $this->prophesize(DataAccessClassFromTableFieldProviderInterface::class);
        $this->classFromTableFieldProviderDataAccessMock->getFieldClassNameFromDictionaryValues('cms_config', 'name')->willReturn('TCMSFieldVarChar');
        $this->classFromTableFieldProviderDataAccessMock->getFieldClassNameFromDictionaryValues('cms_config', 'missing_field')->willReturn(null);
        $this->classFromTableFieldProviderDataAccessMock->getFieldClassNameFromDictionaryValues('cms_config', 'empty_class')->willReturn('');

        $this->classFromTableFieldProvider = new ClassFromTableFieldProvider($this->classFromTableFieldProviderDataAccessMock->reveal());
    }

    private function whenGetFieldClassNameFromTableFieldIsCalled(string $tableField): void
    {
        $this->actualResult = $this->classFromTableFieldProvider->getFieldClassNameFromTableField($tableField);
    }

    private function thenTheReturnedClassNameShouldMatch(?string $className): void
    {
        $this->assertEquals($className, $this->actualResult);
    }

    private function whenGetDictionaryFromTableFieldIsCalled(string $tableField): void
    {
        $this->actualResult = $this->classFromTableFieldProvider->getDictionaryFromTableField($tableField);
    }

    private function thenTheReturnedDictionaryShouldMatch(?array $expectedDictionary): void
    {
        $this->assertEquals($expectedDictionary, $this->actualResult);
    }

    private function thenAnInvalidArgumentExpectionIsExpected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
    }
}
