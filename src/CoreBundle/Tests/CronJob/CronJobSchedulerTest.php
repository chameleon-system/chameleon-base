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
        $utc = new \DateTimeZone('UTC');

        $schedule24Hours = new CronJobScheduleDataModel(
            1440,
            2880,
            false,
            \DateTime::createFromFormat('Y-m-d H:i:s', '2019-11-14 01:15:00', $utc)
        );

        return [
            'requires-execution-exact-24-hours-ago' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 01:15:00',
                    $utc
                ),
                'expectedResult' => true,
            ],
            'requires-execution-25-hours-ago' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 02:15:00',
                    $utc
                ),
                'expectedResult' => true,
            ],
            'no-execution-23-hours-ago' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 00:15:00',
                    $utc
                ),
                'expectedResult' => false,
            ],
            'no-execution-one-second-before-next-planned-job' => [
                'schedule' => $schedule24Hours,
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 01:14:59',
                    $utc
                ),
                'expectedResult' => false,
            ],
            'requires-execution-but-is-locked' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    true,
                    \DateTime::createFromFormat('Y-m-d H:i:s', '2019-11-14 01:15:00', $utc)
                ),
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 02:15:00',
                    $utc
                ),
                'expectedResult' => false,
            ],
            'requires-execution-is-locked-but-lock-timed-out-for-30-min' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    1470,
                    true,
                    \DateTime::createFromFormat('Y-m-d H:i:s', '2019-11-14 01:15:00', $utc)
                ),
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 02:15:01',
                    $utc
                ),
                'expectedResult' => true,
            ],
            '15-min-after-one-oclock-utc-plus-one' => [
                'schedule' => new CronJobScheduleDataModel(
                    1440,
                    2880,
                    true,
                    \DateTime::createFromFormat('Y-m-d H:i:s', '2019-11-14 01:15:00', $utc)
                ),
                'currentUtcTime' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2019-11-15 01:15:05',
                    $utc
                ),
                'expectedResult' => false,
            ],
        ];
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
    ) {
        $this->givenCurrentTimeIs($currentUtcTime);

        $this->whenRequiresExecutionIsCalledWith($schedule);

        $this->thenExpectedResponseIs($expectedResult);
    }

    private function givenCurrentTimeIs(\DateTime $currentUtcTime): void
    {
        $this->mockTimeProvider->getDateTime(Argument::any())->willReturn($currentUtcTime);
//        $this->mockCurrentUtcTime = $currentUtcTime;
    }

    private function whenRequiresExecutionIsCalledWith(CronJobScheduleDataModel $schedule): void
    {
        $this->subjectResult = $this->subject->requiresExecution($schedule);
    }

    private function thenExpectedResponseIs(bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->subjectResult);
    }
}
