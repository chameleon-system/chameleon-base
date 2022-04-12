<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob;

use ChameleonSystem\CoreBundle\CronJob\CronJobFactory;
use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Tests\CronJob\fixtures\CronJobThatExtendsTCMSCronJob;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CronJobFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ContainerInterface|ObjectProphecy
     */
    private $containerMock;
    /**
     * @var CronJobFactory
     */
    private $cronJobFactory;
    /**
     * @var \TCMSCronJob
     */
    private $actualResult;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->containerMock = null;
        $this->cronJobFactory = null;
        $this->actualResult = null;
    }

    public function testConstructCronJobFromService()
    {
        $this->givenACronJobFactory();
        $this->whenConstructCronJobIsCalledForAnExistingCronJob();
        $this->thenTheExpectedCronJobShouldBeReturned();
    }

    private function givenACronJobFactory()
    {
        $this->containerMock = $this->prophesize(ContainerInterface::class);
        $this->containerMock->has('existing_service')->willReturn(true);
        $this->containerMock->has('unknown_identifier')->willReturn(false);
        $this->containerMock->has('service_that_is_unknown_to_the_service_container')->willReturn(false);
        $this->containerMock->has('ChameleonSystem\CoreBundle\Tests\CronJob\fixtures\CronJobThatExtendsTCMSCronJob')->willReturn(false);
        $this->containerMock->has('ChameleonSystem\CoreBundle\Tests\CronJob\fixtures\CronJobThatDoesNotExtendTCMSCronJob')->willReturn(false);
        $this->containerMock->get('existing_service')->willReturn(new CronJobThatExtendsTCMSCronJob());
        $this->containerMock->get('chameleon_system_core.request_state_hash_provider')->willReturn($this->prophesize(RequestStateHashProviderInterface::class));
        ServiceLocator::setContainer($this->containerMock->reveal());

        $this->cronJobFactory = new CronJobFactory($this->containerMock->reveal());
    }

    private function whenConstructCronJobIsCalledForAnExistingCronJob()
    {
        $this->actualResult = $this->cronJobFactory->constructCronJob('existing_service', [
            'foo' => 'bar',
            'baz' => 'quuz',
        ]);
    }

    private function thenTheExpectedCronJobShouldBeReturned()
    {
        $expected = new CronJobThatExtendsTCMSCronJob();
        $expected->sqlData = [
            'foo' => 'bar',
            'baz' => 'quuz',
        ];
        $this->assertEquals($expected, $this->actualResult);
    }

    public function testConstructCronJobFromClassName()
    {
        $this->givenACronJobFactory();
        $this->whenConstructCronJobIsCalledForAnExistingClassName();
        $this->thenTheExpectedCronJobShouldBeReturned();
    }

    private function whenConstructCronJobIsCalledForAnExistingClassName()
    {
        $this->actualResult = $this->cronJobFactory->constructCronJob(CronJobThatExtendsTCMSCronJob::class, [
            'foo' => 'bar',
            'baz' => 'quuz',
        ]);
    }

    public function testConstructNonExistingCronJob()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->givenACronJobFactory();
        $this->whenConstructCronJobIsCalledForANonExistingCronJob();
    }

    private function whenConstructCronJobIsCalledForANonExistingCronJob()
    {
        $this->cronJobFactory->constructCronJob('unknown_identifier', []);
    }

    public function testConstructCronJobWithWrongType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->givenACronJobFactory();
        $this->whenConstructCronJobIsCalledForAServiceThatDoesNotExtendTCMSCronJob();
    }

    private function whenConstructCronJobIsCalledForAServiceThatDoesNotExtendTCMSCronJob()
    {
        $this->cronJobFactory->constructCronJob(fixtures\CronJobThatDoesNotExtendTCMSCronJob::class, []);
    }

    public function testConstructCronJobUnknownToTheServiceContainer()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->givenACronJobFactory();
        $this->whenConstructCronJobIsCalledForARegisteredCronJobUnknownToTheServiceContainer();
    }

    private function whenConstructCronJobIsCalledForARegisteredCronJobUnknownToTheServiceContainer()
    {
        $this->cronJobFactory->constructCronJob('service_that_is_unknown_to_the_service_container', []);
    }
}
