<?php

namespace ChameleonSystem\CoreBundle\Service;

interface LogViewerServiceInterface
{
    /**
     * @return string[]
     */
    public function getLogFiles(): array;

    public function getLogDirectory(): string;

    /**
     * @return string[]
     */
    public function getLastLines(string $filePath, string $numLines): array;
}
