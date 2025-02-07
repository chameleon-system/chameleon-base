<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Twig;

use ChameleonSystem\CmsDashboardBundle\Library\Interfaces\ColorGeneratorServiceInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DashboardTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly ColorGeneratorServiceInterface $colorGeneratorService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('generate_color', [$this, 'generateColor']),
        ];
    }

    public function generateColor(int $index, int $total, float $opacity = 1.0): string
    {
        return $this->colorGeneratorService->generateColor($index, $total, $opacity);
    }
}
