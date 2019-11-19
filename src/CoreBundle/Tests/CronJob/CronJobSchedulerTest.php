<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob;

use ChameleonSystem\CoreBundle\CronJob\CronJobScheduleDataModel;
use ChameleonSystem\CoreBundle\CronJob\CronJobScheduler;
use ChameleonSystem\CoreBundle\Interfaces\ChameleonTimeProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraints\DateTime;

class CronJobSchedulerTest extends TestCase
{
    /**
     * @var DateTime|null
     */
    private $mockCurrentUtcTime;
    /**
     * @var \DateTimeZone
     */
    private $mockTimeZone;
    /**
     * @var ObjectProphecy|ChameleonTimeProviderInterface
     */
    private $mockTimeProvider;

    /**
     * @var CronJobScheduler
     */
    private $subject;
    /**
     * @var mixed
     */
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
    protected function setUp()
    {
        $this->mockCurrentUtcTime = null;
        $this->mockTimeZone = new \DateTimeZone('UTC');
        /** @var ObjectProphecy|ChameleonTimeProviderInterface $timeProvider */
        $this->mockTimeProvider = $this->prophesize(ChameleonTimeProviderInterface::class);
        $this->mockTimeProvider->getDateTime(Argument::any())->willReturn($this->mockCurrentUtcTime);

        $this->subject = new CronJobScheduler($this->mockTimeProvider->reveal());
    }

    /**
     * @dataProvider provideDataForExecutionNeeded
     *
     * @param CronJobScheduleDataModel $schedule
     * @param \DateTime                $currentUtcTime
     * @param bool                     $expectedResult
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
     *
     * @param CronJobScheduleDataModel $schedule
     * @param \DateTime                $currentUtcTime
     * @param \DateTime                $expectedUtcExecutionTime
     */
    public function testCalculateCurrentPlanedExecutionDateUtc(
        CronJobScheduleDataModel $schedule,
        \DateTime $currentUtcTime,
        \DateTime $expectedUtcExecutionTime
    ): void {
        $this->givenCurrentTimeIs($currentUtcTime);
        $this->whenCalculateCurrentPlanedExecutionDateUtcIsCalledWith($schedule);
        $this->thenThePlannedExecutionDateShouldBe($expectedUtcExecutionTime);
    }

    private function whenCalculateCurrentPlanedExecutionDateUtcIsCalledWith(CronJobScheduleDataModel $schedule): void
    {
        $this->subjectResult = $this->subject->calculateCurrentPlanedExecutionDate($schedule);
    }

    private function thenThePlannedExecutionDateShouldBe(\DateTime $expectedUtcExecutionTime): void
    {
        $this->assertEquals($expectedUtcExecutionTime, $this->subjectResult);
    }

    private function createDate(string $dateString): \DateTime
    {
        $utc = null;//new \DateTimeZone('UTC');

        return \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $dateString,
            $utc
        );
    }
}
