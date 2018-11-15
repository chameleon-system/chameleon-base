<?php

namespace ChameleonSystem\ViewRendererBundle\Bridge\Twig\Loader;

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
class TwigStringLoader implements \Twig_LoaderInterface
{
    public function getSource($name)
    {
        @trigger_error(sprintf('Calling "getSource" on "%s" is deprecated since 1.27. Use getSourceContext() instead.', get_class($this)), E_USER_DEPRECATED);

        return $name;
    }

    public function getSourceContext($name)
    {
        return new \Twig_Source($name, $name);
    }

    public function exists($name)
    {
        return true;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        return true;
    }
}
