<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;
use TTools;

class PreviewModeService implements PreviewModeServiceInterface
{
    private const COOKIE_NAME = 'preview_mode';

    public function __construct(
        private readonly string $hashingSecret,
        private readonly Connection $connection,
        private readonly TTools $tools,
    ) {
    }

    /**
     * {@inheritdoc}
     */
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
                    $previewTokenExists = '1' === $this->connection->fetchOne("SELECT 1 FROM `cms_user` WHERE `preview_token` = ?", [$previewToken]);
                    if (true === $previewTokenExists) {
                        $hash = $this->generateHash(substr($cookieString, 0, $pos));
                        $accessGranted = $hash === substr($cookieString, $pos + 1);
                    }
                }
            }
        }

        return $accessGranted;
    }

    /**
     * {@inheritdoc}
     */
    public function grantPreviewAccess(bool $previewGranted, string $cmsUserId): void
    {
        if (false === $previewGranted) {
            setcookie(self::COOKIE_NAME, '');
            $this->connection->update('cms_user', ['preview_token' => ''], ['id' => $cmsUserId]);

            return;
        }
        $token = $this->tools::GetUUID();
        $this->connection->update('cms_user', ['preview_token' => $token], ['id' => $cmsUserId]);
        setcookie(self::COOKIE_NAME, $token.'|'.$this->generateHash($token));
    }

    private function generateHash(string $toHash): string
    {
        return hash('md5', $toHash.$this->hashingSecret);
    }
}
