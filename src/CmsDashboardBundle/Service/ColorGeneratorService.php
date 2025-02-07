<?php

namespace ChameleonSystem\CmsDashboardBundle\Service;

use ChameleonSystem\CmsDashboardBundle\Library\Interfaces\ColorGeneratorServiceInterface;

class ColorGeneratorService implements ColorGeneratorServiceInterface
{
    public function generateColor(int $index, int $total, float $opacity = 1): string
    {
        $palette = [
            '#20a8d8', // Blue
            '#f86c6b', // Red
            '#f8cb00', // Orange
            '#4dbd74', // Green
            '#6610f2', // Indigo
            '#20c997', // Teal
            '#17a2b8', // Cyan
            '#73818f', // Gray
            '#63c2de', // Light-blue
            '#20a8d8', // Primary
            '#6f42c1', // Purple
            '#c8ced3', // Secondary
            '#4dbd74', // Success
            '#63c2de', // Info
            '#e83e8c', // Pink
            '#ffc107', // Warning
            '#ffc107', // Yellow
            '#f86c6b', // Danger
            '#f0f3f5', // Light
        ];

        $paletteSize = count($palette);

        if ($total <= $paletteSize) {
            $rgb = $this->hexToRgb($palette[$index % $paletteSize]);
        } else {
            $step = ($index / max(1, $total - 1)) * ($paletteSize - 1);
            $startIndex = floor($step);
            $endIndex = ceil($step);

            $startColor = $this->hexToRgb($palette[$startIndex]);
            $endColor = $this->hexToRgb($palette[$endIndex]);

            $factor = $step - $startIndex;
            $r = (1 - $factor) * $startColor[0] + $factor * $endColor[0];
            $g = (1 - $factor) * $startColor[1] + $factor * $endColor[1];
            $b = (1 - $factor) * $startColor[2] + $factor * $endColor[2];

            $rgb = [(int) $r, (int) $g, (int) $b];
        }

        if ($opacity >= 1) {
            // Full opacity: return hex value
            return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
        }

        // With transparency: return rgba() string
        return sprintf('rgba(%d, %d, %d, %s)', $rgb[0], $rgb[1], $rgb[2], $opacity);
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
