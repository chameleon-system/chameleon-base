<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use TTools;

class PreviewModeService implements PreviewModeServiceInterface
{
    public const COOKIE_NAME = 'preview_mode';
    private const DEFAULT_PREVIEW_COOKIE_LIFETIME = 3 * 86400; // 3 days

    /**
     * @param array<string, mixed> $sessionOptions
     */
    public function __construct(
        protected readonly string $hashingSecret,
        protected readonly Connection $connection,
        protected readonly TTools $tools,
        protected readonly array $sessionOptions,
    ) {
    }

    public function currentSessionHasPreviewAccess(): bool
    {
        static $accessGranted = null;
        if (null === $accessGranted) {
            $accessGranted = false;
            if (true === isset($_COOKIE[self::COOKIE_NAME])) {
                $cookieString = $_COOKIE[self::COOKIE_NAME];
                $pos = strpos($cookieString, '|');
                if (false !== $pos) {
                    $previewToken = substr($cookieString, 0, $pos);

                    try {
                        $previewTokenExists = $this->previewTokenExists($previewToken);
                    } catch (Exception) {
                        return false; // ignore if field not exists yet
                    }

                    if (true === $previewTokenExists) {
                        $hash = $this->generateHash(substr($cookieString, 0, $pos));
                        $accessGranted = $hash === substr($cookieString, $pos + 1);
                    }
                }
            }
        }

        return $accessGranted;
    }

    public function grantPreviewAccess(bool $previewGranted, string $cmsUserId): void
    {
        try {
            if (false === $previewGranted) {
                setcookie(self::COOKIE_NAME, '', time() - 3600, '/', '', false, true);
                $this->connection->update('cms_user', ['preview_token' => ''], ['id' => $cmsUserId]);

                return;
            }
            $token = $this->tools::GetUUID();
            $this->connection->update('cms_user', ['preview_token' => $token], ['id' => $cmsUserId]);
            setcookie(self::COOKIE_NAME, $token.'|'.$this->generateHash($token), time() + $this->getSessionLifetime(), '/', '', false, true);
        } catch (Exception) {
            // ignore if field not exists yet
        }
    }

    protected function getSessionLifetime(): int
    {
        return $this->sessionOptions['cookie_lifetime'] ?? self::DEFAULT_PREVIEW_COOKIE_LIFETIME;
    }

    protected function generateHash(string $toHash): string
    {
        return hash('md5', $toHash.$this->hashingSecret);
    }

    public function previewTokenExists(string $previewToken): bool
    {
        return 1 === (int)$this->connection->fetchOne("SELECT 1 FROM `cms_user` WHERE `preview_token` = ?", [$previewToken]);
    }
}
