<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\Service\AiImageLabelService;
use ChameleonSystem\CoreBundle\ServiceLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AiImageLabelCompatibilityTest extends TestCase
{
    private ?ContainerInterface $previousContainer;

    protected function setUp(): void
    {
        $property = new \ReflectionProperty(ServiceLocator::class, 'container');
        $this->previousContainer = $property->getValue();
        $container = new ContainerBuilder();
        $container->register('chameleon_system_core.ai_image_label', AiImageLabelService::class)->setPublic(true);
        $container->compile();
        ServiceLocator::setContainer($container);
        foreach (['CHAMELEON_404_IMAGE_PATH_SMALL', 'CHAMELEON_404_IMAGE_PATH_BIG'] as $constant) {
            if (!defined($constant)) {
                define($constant, $constant.'.png');
            }
        }
    }

    protected function tearDown(): void
    {
        (new \ReflectionProperty(ServiceLocator::class, 'container'))->setValue(null, $this->previousContainer);
    }

    public function testEndpointDelegatesWithoutImageFormatBundle(): void
    {
        $image = $this->image();
        self::assertFalse($image->enabled());
        self::assertNull($image->placement());
        self::assertSame(md5('photo-id_400x2002026-10-09 10:00:00'), $image->hash());
        $image->aData['ai_generated_image'] = '1';
        self::assertTrue($image->enabled());
        self::assertSame('dark', $image->variant());
        self::assertSame(md5('photo-id_400x2002026-10-09 10:00:00_ai-label-dark-w10-p2-min5-v3'), $image->hash());
        self::assertSame($this->expectedPlacement('dark'), $image->placement());
    }

    public function testActivationAndVariantOverridesRemainEffective(): void
    {
        $image = $this->image();
        $image->enabledOverride = true;
        $image->variantOverride = 'light';
        self::assertSame(md5('photo-id_400x2002026-10-09 10:00:00_ai-label-light-w10-p2-min5-v3'), $image->hash());
        self::assertSame($this->expectedPlacement('light'), $image->placement());
        $image->aData['ai_generated_image'] = '1';
        $image->enabledOverride = false;
        self::assertNull($image->placement());
        self::assertSame(md5('photo-id_400x2002026-10-09 10:00:00'), $image->hash());
    }

    public function testAssetOverrideRemainsEffective(): void
    {
        $image = $this->image();
        $image->aData['ai_generated_image'] = '1';
        $image->assetOverride = $this->expectedPlacement('light')['assetPath'];
        self::assertSame($this->expectedPlacement('light'), $image->placement());
    }

    private function expectedPlacement(string $variant): array
    {
        return ['assetPath' => dirname(__DIR__, 2).'/Resources/public/images/ai-label-'.$variant.'.png', 'width' => 40, 'height' => 30, 'padding' => 4];
    }

    private function image(): AiImageLabelTestEndpoint
    {
        $image = new AiImageLabelTestEndpoint();
        $image->id = 'photo-id';
        $image->aData = ['path' => 'photo.png', 'time_stamp' => '2026-10-09 10:00:00', 'ai_generated_image' => '0'];

        return $image;
    }
}

class AiImageLabelTestEndpoint extends \TCMSImageEndpoint
{
    public ?bool $enabledOverride = null;
    public ?string $variantOverride = null;
    public ?string $assetOverride = null;

    public function enabled(): bool
    {
        return $this->shouldApplyAiLabel();
    }

    public function variant(): string
    {
        return $this->getAiLabelVariant();
    }

    public function placement(): ?array
    {
        return $this->getAiLabelPlacement(400, 200);
    }

    public function hash(): string
    {
        return $this->getThumbId(400, 200, '');
    }

    protected function shouldApplyAiLabel(): bool
    {
        return $this->enabledOverride ?? parent::shouldApplyAiLabel();
    }

    protected function getAiLabelVariant(): string
    {
        return $this->variantOverride ?? parent::getAiLabelVariant();
    }

    protected function getAiLabelAssetPath(): ?string
    {
        return $this->assetOverride ?? parent::getAiLabelAssetPath();
    }
}
