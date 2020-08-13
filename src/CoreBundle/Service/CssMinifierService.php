<?php

namespace ChameleonSystem\CoreBundle\Service;

use MatthiasMullie\Minify\CSS;

class CssMinifierService implements CssMinifierServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function minify(string $content): string
    {
        $minifier = new CSS();
        $minifier->add($content);

        return $minifier->minify();
    }
}
