<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob;

use ChameleonSystem\CoreBundle\CronJob\CronJobFactory;
use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Tests\CronJob\fixtures\CronJobThatExtendsTCMSCronJob;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CronJobFactoryTest extends TestCase
{
    /**
     * @var MockObject<ContainerInterface>
     */
    private $containerMock;

    /**
     * @var CronJobFactory
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->containerMock = $this->createMock(ContainerInterface::class);
        ServiceLocator::setContainer($this->containerMock);
        $this->addMockServices([
            'existing_service' => fn () => new CronJobThatExtendsTCMSCronJob(),
            'chameleon_system_core.request_state_hash_provider' => $this->createMock(
                RequestStateHashProviderInterface::class
            ),
            'unknown_identifier' => null,
            'ChameleonSystem\CoreBundle\Tests\CronJob\fixtures\CronJobThatExtendsTCMSCronJob' => null,
            'ChameleonSystem\CoreBundle\Tests\CronJob\fixtures\CronJobThatDoesNotExtendTCMSCronJob' => null,
            // Commented out because it is not known to the container.
            // 'service_that_is_unknown_to_the_service_container' => null,
        ]);
        $this->subject = new CronJobFactory($this->containerMock);
    }

    public function testConstructCronJobFromService()
    {
        $data = [
            'foo' => 'bar',
            'baz' => 'quuz',
        ];
        $result = $this->subject->constructCronJob('existing_service', $data);
        $this->assertIsValidCronjobObject($result, $data);
    }

    public function testConstructCronJobFromClassName()
    {
        $data = [
            'foo' => 'bar',
            'baz' => 'quuz',
        ];
        $result = $this->subject->constructCronJob(CronJobThatExtendsTCMSCronJob::class, $data);
        $this->assertIsValidCronjobObject($result, $data);
    }

    public function testConstructNonExistingCronJob()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->constructCronJob('unknown_identifier', []);
    }

    public function testConstructCronJobWithWrongType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->constructCronJob(fixtures\CronJobThatDoesNotExtendTCMSCronJob::class, []);
    }

    public function testConstructCronJobUnknownToTheServiceContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->constructCronJob('service_that_is_unknown_to_the_service_container', []);
    }

    /**
     * @param array $services - assoc array of service name to service object.
     *                        Use `null` as service object to mock not having that service.
     *                        Use a closure to generate services that cannot be created at declare time
     */
    private function addMockServices(array $services): void
    {
        $this->containerMock->method('has')->willReturnCallback(function (string $serviceName) use ($services) {
            return null !== ($services[$serviceName] ?? null);
        });
        $this->containerMock->method('get')->willReturnCallback(function (string $serviceName) use ($services) {
            $service = $services[$serviceName] ?? null;
            if (null === $service) {
                return null;
            }
            if (\is_callable($service)) {
                return $service();
            }

            return $service;
        });
    }

    private function assertIsValidCronjobObject($value, array $expectedData): void
    {
        $this->assertInstanceOf(CronJobThatExtendsTCMSCronJob::class, $value);
        $this->assertEquals(count($expectedData), count($value->sqlData));
        foreach ($expectedData as $key => $expectedValue) {
            $this->assertArrayHasKey($key, $value->sqlData);
            $this->assertEquals($expectedValue, $value->sqlData[$key]);
        }
    }
}
