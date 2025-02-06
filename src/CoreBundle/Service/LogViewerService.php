<?php

namespace ChameleonSystem\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LogViewerService implements LogViewerServiceInterface
{
    public const LOG_DIR = __DIR__.'/../../../../../../var/log';

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function getLogFiles(): array
    {
        $logDirectory = $this->parameterBag->get('kernel.logs_dir');

        if (!is_dir($logDirectory)) {
            return [];
        }

        $files = scandir($logDirectory);

        return array_values(array_filter($files, static function ($file) use ($logDirectory) {
            return is_file($logDirectory.'/'.$file) && 'log' === pathinfo($file, PATHINFO_EXTENSION);
        }));
    }

    public function getLastLines(string $filePath, string $numLines): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $lines = [];
        $fp = fopen($filePath, 'rb');
        if (!$fp) {
            return [];
        }

        fseek($fp, -1, SEEK_END);
        $buffer = '';
        while (ftell($fp) > 0 && \count($lines) < $numLines) {
            $char = fgetc($fp);
            if ("\n" === $char) {
                if (!empty($buffer)) {
                    $lines[] = strrev($buffer);
                    $buffer = '';
                }
            } else {
                $buffer .= $char;
            }
            fseek($fp, -2, SEEK_CUR);
        }
        if (!empty($buffer)) {
            $lines[] = strrev($buffer);
        }

        fclose($fp);

        return array_reverse($lines);
    }
}
