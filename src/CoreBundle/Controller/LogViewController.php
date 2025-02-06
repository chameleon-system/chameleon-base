<?php

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\Service\LogViewerService;
use ChameleonSystem\CoreBundle\Service\LogViewerServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class LogViewController
{
    public function __construct(
        private LogViewerServiceInterface $logViewerService
    ) {
    }

    public function fetchLogContent(string $filename, int $lineCount): Response
    {
        $filePath = LogViewerService::LOG_DIR.'/'.basename($filename);

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
}
