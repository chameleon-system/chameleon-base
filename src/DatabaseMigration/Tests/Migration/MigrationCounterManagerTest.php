<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Counter;

use ChameleonSystem\DatabaseMigration\DataAccess\MigrationDataAccessInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class MigrationCounterManagerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var MigrationDataAccessInterface
     */
    private $migrationDataAccess;
    /**
     * @var MigrationCounterManager
     */
    private $migrationCounterManager;
    private $actualResult;
    /**
     * @var array
     */
    private $createCounterWasCalled = [];

    /**
     * @test
     *
     * @dataProvider getMigrationCounterExistsData
     *
     * @param string $bundleName
     * @param bool $expectedResult
     */
    public function itTellsIfAMigrationCounterExists($bundleName, $expectedResult)
    {
        $this->givenAMigrationDataAccess();
        $this->givenAMigrationCounterManager();
        $this->whenDoesCounterExistsIsCalled($bundleName);
        $this->thenTheExpectedResultShouldBeReturned($expectedResult);
    }

    private function givenAMigrationDataAccess()
    {
        $this->mockMigrationDataAccess();
        $this->migrationDataAccess->getMigrationCounterIdsByBundle()->willReturn([
            'TestBundle' => '42',
        ]);
        $this->migrationDataAccess->createMigrationCounter(Argument::any())->willReturn(null);
        $this->migrationDataAccess->deleteMigrationCounter(Argument::any())->willReturn(null);
        $this->migrationDataAccess->markMigrationFileAsProcessed(Argument::any(), Argument::any())->willReturn(null);
    }

    private function mockMigrationDataAccess()
    {
        $this->migrationDataAccess = $this->prophesize('\ChameleonSystem\DatabaseMigration\DataAccess\MigrationDataAccessInterface');
    }

    private function givenAMigrationCounterManager()
    {
        $this->migrationCounterManager = new MigrationCounterManager($this->migrationDataAccess->reveal());
    }

    /**
     * @param string $bundleName
     */
    private function whenDoesCounterExistsIsCalled($bundleName)
    {
        $this->actualResult = $this->migrationCounterManager->doesCounterExist($bundleName);
    }

    private function thenTheExpectedResultShouldBeReturned($expected)
    {
        static::assertEquals($expected, $this->actualResult);
    }

    /**
     * @return array
     */
    public function getMigrationCounterExistsData()
    {
        return [
            [
                'TestBundle',
                true,
            ],
            [
                'FooBundle',
                false,
            ],
        ];
    }

    /**
     * @test
     */
    public function itCreatesMigrationCounters()
    {
        $this->givenAMigrationDataAccess();
        $this->givenAMigrationCounterManager();
        $this->whenCreateMigrationCounterIsCalled('FooBundle');
        $this->thenTheMigrationCounterShouldBeCreated('FooBundle');
    }

    /**
     * @param string $bundleName
     */
    private function whenCreateMigrationCounterIsCalled($bundleName)
    {
        $this->migrationCounterManager->createMigrationCounter($bundleName);
    }

    /**
     * @param string $bundleName
     */
    private function thenTheMigrationCounterShouldBeCreated($bundleName)
    {
        $this->migrationDataAccess->createMigrationCounter($bundleName)->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itDoesNotCreateDuplicateMigrationCounters()
    {
        $this->givenAMigrationDataAccess();
        $this->givenAMigrationCounterManager();
        $this->whenCreateMigrationCounterIsCalled('TestBundle');
        $this->thenNoMigrationCounterShouldBeCreated();
    }

    private function thenNoMigrationCounterShouldBeCreated()
    {
        $this->migrationDataAccess->createMigrationCounter(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itDeletesMigrationCounters()
    {
        $this->givenAMigrationDataAccess();
        $this->givenAMigrationCounterExists('TestBundle', '2553');
        $this->givenAMigrationCounterManager();
        $this->whenDeleteMigrationCounterIsCalled('TestBundle');
        $this->thenTheMigrationCounterShouldBeDeleted('2553');
    }

    /**
     * @param string $bundleName
     * @param string $counterId
     */
    private function givenAMigrationCounterExists($bundleName, $counterId)
    {
        $this->migrationDataAccess->getMigrationCounterIdsByBundle()->willReturn([
            $bundleName => $counterId,
        ]);
    }

    /**
     * @param string $bundleName
     */
    private function whenDeleteMigrationCounterIsCalled($bundleName)
    {
        $this->migrationCounterManager->deleteMigrationCounter($bundleName);
    }

    /**
     * @param string $counterId
     */
    private function thenTheMigrationCounterShouldBeDeleted($counterId)
    {
        $this->migrationDataAccess->deleteMigrationCounter($counterId)->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itIgnoresDeleteRequestsForNonexistingMigrationCounters()
    {
        $this->givenAMigrationDataAccess();
        $this->givenAMigrationCounterManager();
        $this->whenDeleteMigrationCounterIsCalled('FooBundle');
        $this->thenNoMigrationCounterShouldBeDeleted();
    }

    private function thenNoMigrationCounterShouldBeDeleted()
    {
        $this->migrationDataAccess->deleteMigrationCounter(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itMarksMigrationFilesAsProcessedWhenTheCounterExists()
    {
        $this->givenAMigrationDataAccess();
        $this->givenAMigrationCounterExists('TestBundle', '2553');
        $this->givenAMigrationCounterManager();
        $this->whenMarkMigrationFileAsProcessedIsCalled('TestBundle', 42);
        $this->thenTheMigrationFileShouldBeMarkedAsProcessed('2553', 42);
    }

    /**
     * @param string $bundleName
     * @param int $buildNumber
     */
    private function whenMarkMigrationFileAsProcessedIsCalled($bundleName, $buildNumber)
    {
        $this->migrationCounterManager->markMigrationFileAsProcessed($bundleName, $buildNumber);
    }

    /**
     * @param string $counterId
     * @param int $buildNumber
     */
    private function thenTheMigrationFileShouldBeMarkedAsProcessed($counterId, $buildNumber)
    {
        $this->migrationDataAccess->markMigrationFileAsProcessed($counterId, $buildNumber)->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itMarksMigrationFilesAsProcessedWhenTheCounterDoesNotExist()
    {
        $this->givenAMigrationDataAccessForMarkMigrationFilesAsProcessed();
        $this->givenAMigrationCounterManager();
        $this->whenMarkMigrationFileAsProcessedIsCalled('FooBundle', 42);
        $this->thenTheMigrationFileShouldBeMarkedAsProcessed('2553', 42);
    }

    private function givenAMigrationDataAccessForMarkMigrationFilesAsProcessed()
    {
        $this->givenAMigrationDataAccess();
        $this->migrationDataAccess->createMigrationCounter('FooBundle')->will([$this, 'createCounterCalled']);
        $this->migrationDataAccess->getMigrationCounterIdsByBundle()->will([$this, 'getMigrationCounterIdsByBundle']);
    }

    public function createCounterCalled(array $arguments)
    {
        $this->createCounterWasCalled[$arguments[0]] = true;
    }

    /**
     * @return array
     */
    public function getMigrationCounterIdsByBundle()
    {
        if ($this->doesCounterExist('FooBundle')) {
            return [
                'FooBundle' => '2553',
            ];
        } else {
            return [];
        }
    }

    /**
     * @param string $bundleName
     *
     * @return bool
     */
    public function doesCounterExist($bundleName)
    {
        return array_key_exists($bundleName, $this->createCounterWasCalled);
    }
}
