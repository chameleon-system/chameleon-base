<?php

namespace ChameleonSystem\CoreBundle\Service;

interface CssMinifierServiceInterface
{
    public function minify(string $content): string;
}
