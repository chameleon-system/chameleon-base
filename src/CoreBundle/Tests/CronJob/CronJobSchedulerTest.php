<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\CronJob;

use ChameleonSystem\CoreBundle\CronJob\CronJobScheduler;
use ChameleonSystem\CoreBundle\CronJob\DataModel\CronJobScheduleDataModel;
use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraints\DateTime;

class CronJobSchedulerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var DateTime|null
     */
    private $mockCurrentUtcTime;
    /**
     * @var \DateTimeZone
     */
    private $mockTimeZone;
    /**
     * @var ObjectProphecy|TimeProviderInterface
     */
    private $mockTimeProvider;

    /**
     * @var CronJobScheduler
     */
    private $subject;
    private $subjectResult;

    public function provideDataForExecutionNeeded(): array
    {
        $schedule24Hours = new CronJobScheduleDataModel(
            1440,
            2880,
            false,
            $this->createDate('2019-11-14 01:15:00')
        );

        return [
            'requires-execution-at-midnight' => [
                'schedule' => new CronJobScheduleDataModel(
                    10,
                    2880,
                    false,
                    $this->createDate('2019-11-14 23:15:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 00:00:00'),
                'expectedResult' => true,
            ],

            'requires-execution-exact-24-hours-ago' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => $this->createDate('2019-11-15 01:15:00'),
                'expectedResult' => true,
            ],
            'requires-execution-25-hours-ago' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => $this->createDate('2019-11-15 02:15:00'),
                'expectedResult' => true,
            ],
            'no-execution-23-hours-ago' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => $this->createDate('2019-11-15 00:15:00'),
                'expectedResult' => false,
            ],
            'no-execution-one-second-before-next-planned-job' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => $this->createDate('2019-11-15 01:14:59'),
                'expectedResult' => false,
            ],
            'requires-execution-but-is-locked' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    true,
                    $this->createDate('2019-11-14 01:15:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 02:15:00'),
                'expectedResult' => false,
            ],
            'requires-execution-is-locked-but-lock-timed-out-for-30-min' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    1470,
                    true,
                    $this->createDate('2019-11-14 01:15:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 02:15:01'),
                'expectedResult' => true,
            ],
            'lock-ignored-if-job-is-not-due-to-be-executed' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    10,
                    true,
                    $this->createDate('2019-11-14 01:15:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 00:15:00'),
                'expectedResult' => false,
            ],
            '15-min-after-one-oclock-utc-plus-one' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    false,
                    $this->createDate('2019-11-14 01:15:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-14 01:15:05'),
                'expectedResult' => false,
            ],
            'no-last-execution' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    false,
                    null
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 01:15:05'),
                'expectedResult' => true,
            ],
            'no-last-execution-job-locked' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    true,
                    null
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 01:15:05'),
                'expectedResult' => true,
            ],
        ];
    }

    public function provideDataForExecutionNeededThrowsExceptionOnInvalidInput(): array
    {
        return [
            'invalid-execute-every-n-minutes' => [
                'schedule' => new CronJobScheduleDataModel(
                    0,
                    2880,
                    false,
                    $this->createDate('2019-11-14 23:15:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-15 00:00:00'),
                'expectedException' => new \InvalidArgumentException(sprintf('Invalid schedule value of "0" for executeEveryNMinutes property')),
            ],
        ];
    }

    public function provideDataForCalculateCurrentPlanedExecutionDateUtc(): array
    {
        $locked = [false, true];
        $testCases = [];

        foreach ($locked as $isLocked) {
            $keyName = $isLocked ? 'is-locked-' : 'not-locked-';

            $testCases[$keyName.'run-over-midnight'] = [
                'schedule' => new CronJobScheduleDataModel(
                    10,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-10 23:50:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-11 0:15:00'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-11 0:10:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-not-set'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    null
                ),
                'currentUtcTime' => $this->createDate('2019-11-10 15:00:00'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-10 15:00:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-in-the-future'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-20 15:00:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-10 15:00:00'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-20 15:00:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-in-the-future-but-less-than-execute-every-n-minutes-time'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-20 15:00:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-20 11:00:00'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-20 15:00:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-in-the-past-but-one-second-more-than-execute-every-n-minutes-time'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-20 15:00:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-21 14:59:59'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-20 15:00:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-in-the-past-but-one-second-less-execute-every-n-minutes-time'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-20 15:00:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-21 15:00:01'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-21 15:00:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-in-the-past-but-exactly-as-execute-every-n-minutes-time'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-20 15:00:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-21 15:00:00'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-21 15:00:00'),
            ];

            $testCases[$keyName.'last-planned-date-is-in-the-past-twice-as-execute-every-n-minutes-time'] = [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    $isLocked,
                    $this->createDate('2019-11-20 15:00:00')
                ),
                'currentUtcTime' => $this->createDate('2019-11-22 15:00:00'),
                'expectedUtcExecutionTime' => $this->createDate('2019-11-22 15:00:00'),
            ];
        }

        return $testCases;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockCurrentUtcTime = null;
        $this->mockTimeZone = new \DateTimeZone('UTC');
        $this->mockTimeProvider = $this->prophesize(TimeProviderInterface::class);
        $this->mockTimeProvider->getDateTime(Argument::any())->willReturn($this->mockCurrentUtcTime);

        $this->subject = new CronJobScheduler($this->mockTimeProvider->reveal());
    }

    /**
     * @dataProvider provideDataForExecutionNeeded
     */
    public function testExecutionNeeded(
        CronJobScheduleDataModel $schedule,
        \DateTime $currentUtcTime,
        bool $expectedResult
    ): void {
        $this->givenCurrentTimeIs($currentUtcTime);

        $this->whenRequiresExecutionIsCalledWith($schedule);

        $this->thenExpectedResponseIs($expectedResult);
    }

    /**
     * @dataProvider provideDataForExecutionNeededThrowsExceptionOnInvalidInput
     */
    public function testExecutionNeededThrowsExceptionOnInvalidInput(
        CronJobScheduleDataModel $schedule,
        \DateTime $currentUtcTime,
        \Exception $expectedException
    ): void {
        $this->givenCurrentTimeIs($currentUtcTime);

        $this->thenExceptionIsExpected($expectedException);
        $this->whenRequiresExecutionIsCalledWith($schedule);
    }

    private function givenCurrentTimeIs(\DateTime $currentUtcTime): void
    {
        $this->mockTimeProvider->getDateTime(Argument::any())->willReturn($currentUtcTime);
    }

    private function whenRequiresExecutionIsCalledWith(CronJobScheduleDataModel $schedule): void
    {
        $this->subjectResult = $this->subject->requiresExecution($schedule);
    }

    private function thenExpectedResponseIs(bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->subjectResult);
    }

    /**
     * @dataProvider provideDataForCalculateCurrentPlanedExecutionDateUtc
     */
    public function testCalculateCurrentPlanedExecutionDate(
        CronJobScheduleDataModel $schedule,
        \DateTime $currentUtcTime,
        \DateTime $expectedUtcExecutionTime
    ): void {
        $this->givenCurrentTimeIs($currentUtcTime);
        $this->whenCalculateCurrentPlanedExecutionDateUtcIsCalledWith($schedule);
        $this->thenThePlannedExecutionDateShouldBe($expectedUtcExecutionTime);
    }

    /**
     * @dataProvider provideDataForExecutionNeededThrowsExceptionOnInvalidInput
     */
    public function testCalculateCurrentPlanedExecutionDateThrowsExceptionOnInvalidInput(
        CronJobScheduleDataModel $schedule,
        \DateTime $currentUtcTime,
        \Exception $expectedException
    ): void {
        $this->givenCurrentTimeIs($currentUtcTime);

        $this->thenExceptionIsExpected($expectedException);
        $this->whenCalculateCurrentPlanedExecutionDateUtcIsCalledWith($schedule);
    }

    private function whenCalculateCurrentPlanedExecutionDateUtcIsCalledWith(CronJobScheduleDataModel $schedule): void
    {
        $this->subjectResult = $this->subject->calculateCurrentPlannedExecutionDate($schedule);
    }

    private function thenThePlannedExecutionDateShouldBe(\DateTime $expectedUtcExecutionTime): void
    {
        $this->assertEquals($expectedUtcExecutionTime, $this->subjectResult);
    }

    private function createDate(string $dateString): \DateTime
    {
        $utc = null; // new \DateTimeZone('UTC');

        return \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $dateString,
            $utc
        );
    }

    private function thenExceptionIsExpected(\Exception $expectedException): void
    {
        $this->expectExceptionObject($expectedException);
    }
}
