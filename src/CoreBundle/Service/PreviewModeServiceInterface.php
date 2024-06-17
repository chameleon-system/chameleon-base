<?php

namespace ChameleonSystem\CoreBundle\Service;

interface PreviewModeServiceInterface
{
    public function currentSessionHasPreviewAccess(): bool;

    public function grantPreviewAccess(bool $previewGranted, string $cmsUserId): void;
}
