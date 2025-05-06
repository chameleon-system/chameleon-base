<?php
/**
 * This file is part of the Chameleon System TrackViewsBundle.
 *
 * Adds a Twig function to render the tracking pixel in Twig templates.
 */

namespace ChameleonSystem\TrackViewsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrackViewsExtension extends AbstractExtension
{
    /**
     * Register Twig functions.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('track_views_pixel', [$this, 'renderPixel'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Render the tracking pixel HTML.
     */
    public function renderPixel(): string
    {
        return \TPkgTrackObjectViews::GetInstance()?->Render();
    }
}
