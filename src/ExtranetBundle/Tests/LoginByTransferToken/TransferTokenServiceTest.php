<?php

namespace ChameleonSystem\ExtranetBundle\Tests\LoginByTransferToken;

use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ChameleonSystem\ExtranetBundle\LoginByTransferToken\TransferTokenService;
use Psr\Log\LoggerInterface;

class TransferTokenServiceTest extends TestCase
{
    /** @var MockObject<TimeProviderInterface> */
    protected $timeProvider;

    /** @var MockObject<LoggerInterface> */
    protected $logger;

    /** @var int */
    protected $currentTime;

    public function setUp(): void
    {
        $this->currentTime = time();

        $this->logger = $this->createMock(LoggerInterface::class);

        $this->timeProvider = $this->createMock(TimeProviderInterface::class);
        $this->timeProvider
            ->method('getUnixTimestamp')
            ->willReturnCallback(function() {
                return $this->currentTime;
            });
    }

    public function testCreatesDifferentTokenWithEveryCall(): void
    {
        $service = $this->service('secret');
        $this->assertNotEquals(
            $service->createTransferTokenForUser(11, 120),
            $service->createTransferTokenForUser(11, 120)
        );
    }

    public function testTokenCreatedByServiceCanBeInterpretedByService(): void
    {
        $service = $this->service('secret');
        $token = $service->createTransferTokenForUser(11, 120);
        $this->assertEquals(11, $service->getUserIdFromTransferToken($token));
    }

    public function testTokenIsInvalidIfExpired(): void
    {
        $service = $this->service('secret');

        $this->pretendTimeIs('2020-10-10 10:00:00');
        $token = $service->createTransferTokenForUser(11, 120);

        $this->pretendTimeIs('2020-10-10 10:03:00');
        $this->assertNull($service->getUserIdFromTransferToken($token));
    }

    public function testRefusesToDecodeTokenWhenSecretIsDefault(): void
    {
        $service = $this->service('!ThisTokenIsNotSoSecretChangeIt!');

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->callback(function($message) {
                return str_contains($message, 'Refusing to encode or decode transfer tokens with default secret');
            }));

        $token = $service->createTransferTokenForUser(11, 120);
        $this->assertNull($service->getUserIdFromTransferToken($token));
    }

    public function testRandomStringIsNotValidToken(): void
    {
        $this->assertNull($this->service('secret')->getUserIdFromTransferToken('foobar'));
    }

    public function testTokenCreatedWithDifferentSecretIsNotValid(): void
    {
        $token = $this->service('secret1')->createTransferTokenForUser(11, 120);
        $this->assertNull($this->service('secret2')->getUserIdFromTransferToken($token));
    }

    private function service(string $secret): TransferTokenService
    {
        return new TransferTokenService(
            $this->timeProvider,
            $this->logger,
            $secret,
            'aes128'
        );
    }

    private function pretendTimeIs(string $time): void
    {
        $this->currentTime = (new \DateTime($time))->getTimestamp();
    }

}
