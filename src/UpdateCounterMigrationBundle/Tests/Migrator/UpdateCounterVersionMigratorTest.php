<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Tests\Migrator;

use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\BundleDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;
use ChameleonSystem\UpdateCounterMigrationBundle\Migrator\UpdateCounterVersionMigrator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class UpdateCounterVersionMigratorTest extends TestCase
{
    /**
     * @var UpdateCounterVersionMigrator
     */
    private $subject;
    /**
     * @var CounterMigrationDataAccessInterface|ObjectProphecy
     */
    private $counterMigrationDataAccessMock;
    /**
     * @var BundleDataAccessInterface|ObjectProphecy
     */
    private $bundleDataAccessMock;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->subject = null;
        $this->counterMigrationDataAccessMock = null;
        $this->bundleDataAccessMock = null;
    }

    /**
     * @dataProvider provideDataForTestMigrateToVersionTwo
     *
     * @param array $oldCounters
     * @param array $newCounters
     */
    public function testMigrateToVersionTwo(array $oldCounters, array $newCounters): void
    {
        $this->givenUpdateCounterVersionMigrator();
        $this->givenBundlesAreRegistered();
        $this->givenMigrationToVersionTwoIsRequired();
        $this->givenOldCountersExist($oldCounters);

        $this->whenMigrateIsCalled();

        $this->thenMigrationTablesShouldBeCreated();
        $this->thenUpdateCountersShouldBeMigrated($newCounters);
        $this->thenMigrationCounterVersionShouldBeIncreased();
    }

    public function provideDataForTestMigrateToVersionTwo(): array
    {
        return [
            'empty' => [
                'oldCounters' => [],
                'newCounters' => [],
            ],
            'chameleon-package-simple' => [
                'oldCounters' => [
                    'dbversion-meta-packages-plugin-bundle' => [12345, 34567, 56789],
                ],
                'newCounters' => [
                    'ChameleonSystemPluginBundle' => [12345, 34567, 56789],
                ],
            ],
            'project-package-simple' => [
                'oldCounters' => [
                    'dbversion-meta-packages-ProjectBundle/Script' => [12345, 34567, 56789],
                ],
                'newCounters' => [
                    'ProjectBundle' => [12345, 34567, 56789],
                ],
            ],
            'chameleon-base-bundle' => [
                'oldCounters' => [
                    'dbversion-meta-packages-pkgcmsclassmanager/pkgCmsClassManager-updates' => [1, 2, 3, 4, 5],
                ],
                'newCounters' => [
                    'ChameleonSystemCmsClassManagerBundle' => [1, 2, 3, 4, 5],
                ],
            ],
            'conflicting-names-in-vendor-and-project' => [
                'oldCounters' => [
                    'dbversion-meta-packages-pkgnewsletter/pkgNewsletter-updates' => [1, 2],
                    'dbversion-meta-packages-NewsletterBundle/Script' => [2, 3],
                ],
                'newCounters' => [
                    'ChameleonSystemNewsletterBundle' => [1, 2],
                    'NewsletterBundle' => [2, 3],
                ],
            ],
            'core-module-combine-counters' => [
                'oldCounters' => [
                    'dbversion-meta-module-Script' => [1, 2],
                    'dbversion-meta-module-updates' => [1, 4],
                ],
                'newCounters' => [
                    'ChameleonSystemCoreBundle' => [1, 2, 4],
                ],
            ],
            'shop-module' => [
                'oldCounters' => [
                    'dbversion-meta-module-pkgShop' => [1, 2],
                ],
                'newCounters' => [
                    'ChameleonSystemShopBundle' => [1, 2],
                ],
            ],
            'customer-combine-counters' => [
                'oldCounters' => [
                    'dbversion-meta-customer-updates' => [1, 2],
                    'dbversion-meta-customer-foo' => [2, 3],
                ],
                'newCounters' => [
                    'EsonoCustomerBundle' => [1, 2, 3],
                ],
            ],
            'second-customer' => [
                'oldCounters' => [
                    'dbversion-meta-customer-updates' => [1, 2],
                    'dbversion-meta-packages-CustomerBundle/Script' => [2, 3],
                ],
                'newCounters' => [
                    'EsonoCustomerBundle' => [1, 2],
                    'SecondCustomerBundle' => [2, 3],
                ],
            ],
        ];
    }

    public function testMigrateToVersionTwoNotRequired(): void
    {
        $this->givenUpdateCounterVersionMigrator();
        $this->givenMigrationTwoVersionTwoIsNotRequired();

        $this->whenMigrateIsCalled();

        $this->thenMigrationTablesShouldNotBeCreated();
        $this->thenUpdateCountersShouldNotBeMigrated();
        $this->thenMigrationCounterVersionShouldNotBeIncreased();
    }

    /**
     * @dataProvider provideDataForTestMigrateInvalidCounters
     *
     * @param array $oldCounters
     * @param array $expectedInvalidCounters
     */
    public function testMigrateToVersionTwoInvalidCounters(array $oldCounters, array $expectedInvalidCounters): void
    {
        $this->givenUpdateCounterVersionMigrator();
        $this->givenBundlesAreRegistered();
        $this->givenMigrationToVersionTwoIsRequired();
        $this->givenOldCountersExist($oldCounters);

        try {
            $this->whenMigrateIsCalled();
            $this->thenExecutionShouldNotCompleteWithoutException();
        } catch (InvalidMigrationCounterException $e) {
            $this->thenInvalidMigrationCounterExceptionShouldContainInvalidCounters($e, $expectedInvalidCounters);
            $this->thenMigrationTablesShouldBeCreated();
            $this->thenUpdateCountersShouldNotBeMigrated();
            $this->thenMigrationCounterVersionShouldNotBeIncreased();
        }
    }

    public function provideDataForTestMigrateInvalidCounters(): array
    {
        return [
            'single-invalid' => [
                'oldCounters' => [
                    'dbversion-meta-packages-unknown-updates' => [1, 2],
                ],
                'expectedInvalidCounters' => [
                    'dbversion-meta-packages-unknown-updates',
                ],
            ],
            'some-invalid' => [
                'oldCounters' => [
                    'dbversion-meta-packages-pkgcmsclassmanager/pkgCmsClassManager-updates' => [1, 2, 3, 4, 5],
                    'dbversion-meta-packages-unknown-updates' => [1, 2],
                    'dbversion-meta-packages-invalid-updates' => [1, 2, 42],
                    'dbversion-meta-customer-updates' => [1],
                ],
                'expectedInvalidCounters' => [
                    'dbversion-meta-packages-unknown-updates',
                    'dbversion-meta-packages-invalid-updates',
                ],
            ],
            'pkgExtranetUserProfile-module' => [
                'oldCounters' => [
                    'dbversion-meta-module-pkgExtranetUserProfile-updates' => [1, 2],
                ],
                'expectedInvalidCounters' => [
                    'dbversion-meta-module-pkgExtranetUserProfile-updates',
                ],
            ],
        ];
    }

    private function givenUpdateCounterVersionMigrator(): void
    {
        $this->counterMigrationDataAccessMock = $this->prophesize(CounterMigrationDataAccessInterface::class);
        $this->counterMigrationDataAccessMock->createMigrationTablesVersionTwo()->will(function () {});
        $this->counterMigrationDataAccessMock->createCountersVersionTwo(Argument::any())->will(function () {});
        $this->counterMigrationDataAccessMock->deleteCountersVersionOne(Argument::any(), Argument::any())->will(function () {});
        $this->counterMigrationDataAccessMock->saveMigrationCounterVersion(Argument::any())->will(function () {});

        $this->bundleDataAccessMock = $this->prophesize(BundleDataAccessInterface::class);

        $this->subject = new UpdateCounterVersionMigrator(
            $this->counterMigrationDataAccessMock->reveal(),
            $this->bundleDataAccessMock->reveal()
        );
    }

    private function givenBundlesAreRegistered(): void
    {
        $this->bundleDataAccessMock->getBundlePaths()->willReturn([
            'ChameleonSystemPluginBundle' => '/path/to/vendor/chameleon-system/plugin-bundle',
            'ChameleonSystemCmsClassManagerBundle' => '/path/to/vendor/chameleon-system/chameleon-base/src/CmsClassManagerBundle',
            'ThirdPartyBundle' => '/path/to/vendor/third-party/third-party-bundle',
            'ProjectBundle' => '/path/to/src/ProjectBundle',
            'EsonoCustomerBundle' => '/path/to/src/Esono/CustomerBundle',
            'NewsletterBundle' => '/path/to/src/Esono/NewsletterBundle',
            'ChameleonSystemNewsletterBundle' => '/path/to/vendor/chameleon-system/chameleon-base/src/NewsletterBundle',
            'SecondCustomerBundle' => '/path/to/vendor/second-customer/customer-bundle',
        ]);
    }

    private function givenMigrationToVersionTwoIsRequired(): void
    {
        $this->counterMigrationDataAccessMock->getMigrationCounterVersion()->willReturn(1);
    }

    private function givenMigrationTwoVersionTwoIsNotRequired(): void
    {
        $this->counterMigrationDataAccessMock->getMigrationCounterVersion()->willReturn(2);
    }

    private function givenOldCountersExist(array $oldCounters): void
    {
        $oldCounterMock = [];
        foreach ($oldCounters as $name => $buildNumbers) {
            $oldCounterMock[] = [
                'systemname' => $name,
                'value' => \json_encode([
                    'buildNumbers' => $buildNumbers,
                ]),
            ];
        }
        $this->counterMigrationDataAccessMock->getAllCountersVersionOne()->willReturn($oldCounterMock);
    }

    /**
     * @throws InvalidMigrationCounterException
     */
    private function whenMigrateIsCalled(): void
    {
        $this->subject->migrate();
    }

    private function thenMigrationTablesShouldBeCreated(): void
    {
        $this->counterMigrationDataAccessMock->createMigrationTablesVersionTwo()->shouldHaveBeenCalled();
    }

    private function thenMigrationTablesShouldNotBeCreated(): void
    {
        $this->counterMigrationDataAccessMock->createMigrationTablesVersionTwo()->shouldNotHaveBeenCalled();
    }

    private function thenUpdateCountersShouldBeMigrated(array $newCounters): void
    {
        $this->counterMigrationDataAccessMock->createCountersVersionTwo($newCounters)->shouldHaveBeenCalled();
        $this->counterMigrationDataAccessMock->deleteCountersVersionOne('dbversion-%', [
            'dbversion-counter',
            'dbversion-timestamp',
        ])->shouldHaveBeenCalled();
    }

    private function thenUpdateCountersShouldNotBeMigrated(): void
    {
        $this->counterMigrationDataAccessMock->createCountersVersionTwo(Argument::any())->shouldNotHaveBeenCalled();
        $this->counterMigrationDataAccessMock->deleteCountersVersionOne(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
    }

    private function thenMigrationCounterVersionShouldBeIncreased(): void
    {
        $this->counterMigrationDataAccessMock->saveMigrationCounterVersion(2)->shouldHaveBeenCalled();
    }

    private function thenMigrationCounterVersionShouldNotBeIncreased(): void
    {
        $this->counterMigrationDataAccessMock->saveMigrationCounterVersion(Argument::any())->shouldNotHaveBeenCalled();
    }

    private function thenInvalidMigrationCounterExceptionShouldContainInvalidCounters(InvalidMigrationCounterException $e, array $expectedInvalidCounters)
    {
        $this->assertEquals($expectedInvalidCounters, $e->getInvalidCounters());
    }

    private function thenExecutionShouldNotCompleteWithoutException(): void
    {
        self::fail('Expected InvalidMigrationCounterException.');
    }
}
