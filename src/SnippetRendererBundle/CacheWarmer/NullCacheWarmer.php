<?php

namespace ChameleonSystem\SnippetRendererBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class NullCacheWarmer implements CacheWarmerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $cacheDir
     * @return void
     */
    public function warmUp($cacheDir)
    {
        // noop
    }
}
