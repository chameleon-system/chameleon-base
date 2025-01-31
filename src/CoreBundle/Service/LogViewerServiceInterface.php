<?php

namespace ChameleonSystem\CoreBundle\Service;

interface LogViewerServiceInterface
{
    /**
     * @return String[]
     */
    public function getLogFiles(): array;

    /**
     * @return String[]
     */
    public function getLastLines(string $filePath, string $numLines): array;
}