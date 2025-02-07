<?php

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\Service\LogViewerServiceInterface;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class LogViewController
{
    public function __construct(
        private LogViewerServiceInterface $logViewerService,
        private Security $security
    ) {
    }

    public function fetchLogContent(string $filename, int $lineCount): Response
    {
        if (false === $this->hasAdminRole()) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $filePath = $this->logViewerService->getLogDirectory().'/'.basename($filename);

        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'File not found'], 404);
        }

        if (!is_readable($filePath)) {
            return new JsonResponse(['error' => 'File is not readable'], 403);
        }

        $lines = $this->logViewerService->getLastLines($filePath, $lineCount);

        return new JsonResponse([
            'filename' => $filename,
            'lines' => implode("\n", $lines),
        ]);
    }

    private function hasAdminRole(): bool
    {
        $user = $this->security->getUser();

        if (null === $user) {
            return false;
        }

        return $this->security->isGranted(CmsUserRoleConstants::CMS_ADMIN);
    }
}
