<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\Service\AiImageLabelService;
use PHPUnit\Framework\TestCase;

class AiImageLabelServiceTest extends TestCase
{
    /** @dataProvider enabledValues */
    public function testActivation($value, bool $expected): void
    {
        self::assertSame($expected, (new AiImageLabelService())->isEnabled(['ai_generated_image' => $value]));
    }

    public static function enabledValues(): array
    {
        return [[true, true], [1, true], ['1', true], [false, false], [0, false], ['0', false], [null, false], ['yes', false]];
    }

    public function testDefaultsAndCacheSuffixes(): void
    {
        $service = new AiImageLabelService();
        self::assertFalse($service->isEnabled([]));
        self::assertSame('dark', $service->getVariant([]));
        self::assertSame('dark', $service->getVariant(['ai_label_variant' => 'invalid']));
        self::assertSame('light', $service->getVariant(['ai_label_variant' => 'light']));
        self::assertSame('', $service->getCacheKeySuffix(false, 'light'));
        self::assertSame('_ai-label-dark-w10-p2-min5-v3', $service->getCacheKeySuffix(true, 'dark'));
        self::assertSame('_ai-label-light-w10-p2-min5-v3', $service->getCacheKeySuffix(true, 'light'));
        self::assertNull($service->getPlacement([], 400, 200));
        self::assertNull($service->calculatePlacement(null, 400, 200));
    }

    /** @dataProvider placements */
    public function testPlacementUsesExistingRules(int $width, int $height, ?array $expected): void
    {
        $service = new AiImageLabelService();
        foreach (['dark', 'light'] as $variant) {
            $asset = dirname(__DIR__, 2).'/Resources/public/images/ai-label-'.$variant.'.png';
            self::assertSame($asset, $service->getAssetPath($variant));
            self::assertSame(
                null === $expected ? null : ['assetPath' => $asset] + $expected,
                $service->getPlacement(['ai_generated_image' => '1', 'ai_label_variant' => $variant], $width, $height)
            );
        }
    }

    public static function placements(): array
    {
        return [
            'standard' => [400, 200, ['width' => 40, 'height' => 30, 'padding' => 4]],
            'frontend' => [690, 518, ['width' => 69, 'height' => 52, 'padding' => 10]],
            'asset size limit' => [2000, 1000, ['width' => 154, 'height' => 116, 'padding' => 20]],
            'below minimum width' => [20, 5, null],
            'insufficient height' => [400, 20, null],
        ];
    }
}
