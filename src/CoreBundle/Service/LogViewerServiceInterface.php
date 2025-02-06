<?php

namespace ChameleonSystem\CoreBundle\Service;

interface LogViewerServiceInterface
{
    /**
     * @return string[]
     */
    public function getLogFiles(): array;

    /**
     * @return string[]
     */
    public function getLastLines(string $filePath, string $numLines): array;
}
