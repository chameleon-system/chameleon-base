<?php

namespace ChameleonSystem\CoreBundle\Service;

class LogViewerService implements LogViewerServiceInterface
{
    public const LOG_DIR = __DIR__.'/../../../../../../var/log';

    public function getLogFiles(): array
    {
        if (!is_dir(self::LOG_DIR)) {
            return [];
        }

        $files = scandir(self::LOG_DIR);
        return array_values(array_filter($files, static function ($file) {
            return is_file(self::LOG_DIR . '/' . $file) && 'log' === pathinfo($file, PATHINFO_EXTENSION);
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