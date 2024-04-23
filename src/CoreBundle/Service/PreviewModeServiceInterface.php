<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Exception;

interface PreviewModeServiceInterface
{
    /**
     * @throws Exception
     */
    public function currentSessionHasPreviewAccess(): bool;

    /**
     * @throws Exception
     */
    public function grantPreviewAccess(bool $previewGranted, string $cmsUserId): void;
}
