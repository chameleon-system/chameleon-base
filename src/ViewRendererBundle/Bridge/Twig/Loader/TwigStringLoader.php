<?php

namespace ChameleonSystem\ViewRendererBundle\Bridge\Twig\Loader;

use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Loads a template from a string. In the Chameleon System this class is only used in case a template could not be
 * found. We can then display the template name so that developers can more easily locate errors.
 *
 * The class is taken from Twig 1.x, the original docblock follows.
 *
 * ---
 *
 * This loader should NEVER be used. It only exists for Twig internal purposes.
 *
 * When using this loader with a cache mechanism, you should know that a new cache
 * key is generated each time a template content "changes" (the cache key being the
 * source code of the template). If you don't want to see your cache grows out of
 * control, you need to take care of clearing the old cache file by yourself.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigStringLoader implements LoaderInterface
{
    public function getSourceContext(string $name): Source
    {
        return new Source($name, $name);
    }

    public function exists(string $name): bool
    {
        return true;
    }

    public function getCacheKey(string $name): string
    {
        return $name;
    }

    public function isFresh(string $name, int $time): bool
    {
        return true;
    }
}
