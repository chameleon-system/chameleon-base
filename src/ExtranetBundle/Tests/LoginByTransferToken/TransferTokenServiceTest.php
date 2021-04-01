<?php

namespace ChameleonSystem\ExtranetBundle\Tests\LoginByTransferToken;

use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockClass;
use ChameleonSystem\ExtranetBundle\LoginByTransferToken\TransferTokenService;

class TransferTokenServiceTest extends TestCase
{
    /** @var MockClass<TimeProviderInterface> */
    protected $timeProvider;

    /** @var int */
    protected $currentTime;

    public function setUp(): void
    {
        $this->currentTime = time();

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
            $service->createTransferTokenForUser(11, 120),
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
            $secret,
            'aes128'
        );
    }

    private function pretendTimeIs(string $time): void
    {
        $this->currentTime = (new \DateTime($time))->getTimestamp();
    }

}
