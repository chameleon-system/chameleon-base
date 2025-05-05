<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\LoginByToken;

use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;
use Psr\Log\LoggerInterface;

class LoginTokenService implements LoginTokenServiceInterface
{
    private const DEFAULT_TOKEN = '!ThisTokenIsNotSoSecretChangeIt!';

    /** @var TimeProviderInterface */
    private $timeProvider;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $secret;

    /** @var string */
    private $algorithm;

    public function __construct(
        TimeProviderInterface $timeProvider,
        LoggerInterface $logger,
        string $secret,
        string $algorithm
    ) {
        $this->timeProvider = $timeProvider;
        $this->logger = $logger;
        $this->secret = $secret;
        $this->algorithm = $algorithm;
    }

    /** {@inheritdoc} */
    public function createTokenForUser(
        string $userId,
        int $expiresAfterSeconds
    ): string {
        return $this->encodeToken([
            'userId' => $userId,
            'expires' => $this->timeProvider->getUnixTimestamp() + $expiresAfterSeconds,
        ]);
    }

    /** {@inheritdoc} */
    public function getUserIdFromToken(string $token): ?string
    {
        $data = $this->decodeToken($token);
        if (null === $data
            || false === array_key_exists('expires', $data)
            || false === array_key_exists('userId', $data)
        ) {
            return null;
        }

        if ($this->timeProvider->getUnixTimestamp() > $data['expires']) {
            return null;
        }

        return $data['userId'];
    }

    private function encodeToken(array $data): string
    {
        $data['_salt'] = md5(random_bytes(16));
        $json = json_encode($data);
        $encrypted = openssl_encrypt(
            $json,
            $this->algorithm,
            $this->secret,
            0,
            $this->initializationVector()
        );

        return base64_encode($encrypted);
    }

    private function decodeToken(string $token): ?array
    {
        if (false === $this->isReadyToEncodeTokens()) {
            return null;
        }

        $encrypted = base64_decode($token);
        if (false === $encrypted) {
            return null;
        }

        $json = openssl_decrypt(
            $encrypted,
            $this->algorithm,
            $this->secret,
            0,
            $this->initializationVector()
        );
        if (false === $json) {
            return null;
        }

        $data = json_decode($json, true);
        if (false === is_array($data)) {
            return null;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function isReadyToEncodeTokens(): bool
    {
        if (self::DEFAULT_TOKEN === $this->secret) {
            $this->logger->error(sprintf('
                Refusing to encode or decode tokens with default secret.
                Please ensure that the secret is set to a random string that is not 
                `%s`
            ', self::DEFAULT_TOKEN));

            return false;
        }

        return true;
    }

    private function initializationVector(): string
    {
        return substr(md5($this->secret), 0, 16);
    }
}
